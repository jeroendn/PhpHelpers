#!/usr/bin/env bash
#
# code-quality-assurance — whole-project quality gate, meant as the final check
# before opening a PR.
#
# Ships with jeroendn/php-helpers; composer links it into vendor/bin/ of every
# project that requires the package. Run it from anywhere inside a project:
#
#   vendor/bin/code-quality-assurance.sh
#
# Steps run in sequence and the FIRST failure aborts. Every step is optional:
# the tool steps (3-6) run through the project's composer script definitions
# ("scripts" in composer.json), so a step only runs when the project defines
# the matching script — and the project's definition controls the exact
# command, config and flags. One script serves projects with different setups.
#
#   1. composer normalize  needs vendor/ergebnis/composer-normalize
#   2. composer validate   always (a composer.json is required anyway)
#   3. rector              needs a "rector-fix" composer script
#   4. php-cs-fixer        needs a "cs-fix" composer script
#   5. phpstan             needs a "phpstan" composer script
#   6. phpunit             needs a "phpunit" composer script
#   7. npm build           needs npm + a "build" script in package.json
#
# Rector runs before php-cs-fixer so cs-fixer can clean up rector's rewrites.
# Note this is a "prepare", not a pure "check": normalize, rector and cs-fixer
# MUTATE the working tree.

set -uo pipefail

NC='\033[0m'
INFO='\033[1;36m'
WARN='\033[0;33m'
ERROR='\033[1;31m'
SUCCESS='\033[1;32m'

# The project root is the nearest directory upwards containing a composer.json
# (the script itself lives in the vendor dir of that project).
ROOT="$PWD"
while [ ! -f "$ROOT/composer.json" ]; do
    ROOT="$(dirname "$ROOT")"
    if [ "$ROOT" = "/" ]; then
        printf '%b\n' "${ERROR}code-quality-assurance: no composer.json found in $PWD or any parent directory.${NC}" >&2
        exit 1
    fi
done
cd "$ROOT" || exit 1

if command -v composer >/dev/null 2>&1; then
    COMPOSER=(composer)
elif [ -f vendor/bin/composer ]; then
    COMPOSER=(php vendor/bin/composer)
else
    COMPOSER=()
fi

RAN=0
SKIPPED=0

cqa_step() {
    local label="$1" rc; shift
    RAN=$((RAN + 1))
    printf '\n%b\n' "${INFO}▶ code-quality-assurance: ${label}${NC}"
    "$@"
    rc=$?
    if [ "$rc" -ne 0 ]; then
        printf '%b\n' "${ERROR}✗ code-quality-assurance aborted — '${label}' failed.${NC}" >&2
        exit "$rc"
    fi
}

cqa_skip() {
    SKIPPED=$((SKIPPED + 1))
    printf '%b\n' "${WARN}– ${1} skipped: ${2}${NC}"
}

# Output of `composer run-script --list`: one indented script name per line.
COMPOSER_SCRIPTS=""
if [ "${#COMPOSER[@]}" -gt 0 ]; then
    COMPOSER_SCRIPTS="$("${COMPOSER[@]}" run-script --list 2>/dev/null)"
fi

has_composer_script() {
    printf '%s\n' "$COMPOSER_SCRIPTS" | grep -qE "^[[:space:]]+${1}([[:space:]]|$)"
}

# Run a step via the project's composer script definition, or skip when the
# project does not define it.
composer_script_step() {
    local label="$1" script="$2"
    if [ "${#COMPOSER[@]}" -eq 0 ]; then
        cqa_skip "$label" "no composer executable found"
    elif has_composer_script "$script"; then
        cqa_step "$label" "${COMPOSER[@]}" run-script "$script"
    else
        cqa_skip "$label" "no '${script}' composer script defined"
    fi
}

# 1 + 2. composer normalize + validate
if [ "${#COMPOSER[@]}" -eq 0 ]; then
    cqa_skip "composer normalize" "no composer executable found"
    cqa_skip "composer validate" "no composer executable found"
else
    if [ -d vendor/ergebnis/composer-normalize ]; then
        cqa_step "composer normalize" "${COMPOSER[@]}" normalize
    else
        cqa_skip "composer normalize" "ergebnis/composer-normalize is not installed"
    fi
    cqa_step "composer validate" "${COMPOSER[@]}" validate --strict
fi

# 3. rector
composer_script_step "rector" rector-fix

# 4. php-cs-fixer
composer_script_step "cs-fixer" cs-fix

# 5. phpstan
composer_script_step "phpstan" phpstan

# 6. phpunit
composer_script_step "phpunit" phpunit

# 7. npm build
if command -v npm >/dev/null 2>&1; then
    # `npm pkg get` prints {} when the key does not exist in package.json.
    if [ -f package.json ] && [ "$(npm pkg get scripts.build 2>/dev/null)" != '{}' ]; then
        cqa_step "npm build" npm run build
    else
        cqa_skip "npm build" "no package.json with a build script found"
    fi
else
    cqa_skip "npm build" "not installed"
fi

printf '\n%b\n' "${SUCCESS}✓ code-quality-assurance: ${RAN} step(s) passed, ${SKIPPED} skipped${NC}"
