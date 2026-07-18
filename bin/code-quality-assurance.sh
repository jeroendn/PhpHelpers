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
# it only runs when the tool (and the configuration it needs) is present in
# the project the script is invoked in, so one script serves projects with
# different setups. Configuration always comes from the project, never from
# this package.
#
#   1. composer normalize  needs vendor/ergebnis/composer-normalize
#   2. composer validate   always (a composer.json is required anyway)
#   3. rector              needs vendor/bin/rector + rector.php
#   4. php-cs-fixer        needs vendor/bin/php-cs-fixer + .php-cs-fixer(.dist).php
#   5. phpstan             needs vendor/bin/phpstan + phpstan.neon(.dist) or phpstan.dist.neon
#   6. phpunit             needs vendor/bin/phpunit + phpunit.xml(.dist), phpunit.dist.xml or tests/
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

# Echo the first of the given files that exists in the project root.
first_existing() {
    local f
    for f in "$@"; do
        if [ -f "$f" ]; then
            echo "$f"
            return 0
        fi
    done
    return 1
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
    cqa_step "composer validate" "${COMPOSER[@]}" validate
fi

# 3. rector
if [ -f vendor/bin/rector ]; then
    if CONFIG="$(first_existing rector.php rector.dist.php)"; then
        cqa_step "rector" vendor/bin/rector process --config "$CONFIG"
    else
        cqa_skip "rector" "no rector.php found"
    fi
else
    cqa_skip "rector" "not installed"
fi

# 4. php-cs-fixer
if [ -f vendor/bin/php-cs-fixer ]; then
    if CONFIG="$(first_existing .php-cs-fixer.php .php-cs-fixer.dist.php)"; then
        cqa_step "cs-fixer" vendor/bin/php-cs-fixer fix --config "$CONFIG"
    else
        cqa_skip "cs-fixer" "no .php-cs-fixer(.dist).php found"
    fi
else
    cqa_skip "cs-fixer" "not installed"
fi

# 5. phpstan
if [ -f vendor/bin/phpstan ]; then
    if CONFIG="$(first_existing phpstan.neon phpstan.neon.dist phpstan.dist.neon)"; then
        cqa_step "phpstan" vendor/bin/phpstan analyse --configuration "$CONFIG" --memory-limit=2G
    else
        cqa_skip "phpstan" "no phpstan.neon(.dist) or phpstan.dist.neon found"
    fi
else
    cqa_skip "phpstan" "not installed"
fi

# 6. phpunit
if [ -f vendor/bin/phpunit ]; then
    if first_existing phpunit.xml phpunit.xml.dist phpunit.dist.xml >/dev/null; then
        cqa_step "phpunit" vendor/bin/phpunit
    elif [ -d tests ]; then
        cqa_step "phpunit" vendor/bin/phpunit tests
    else
        cqa_skip "phpunit" "no phpunit.xml(.dist), phpunit.dist.xml or tests/ directory found"
    fi
else
    cqa_skip "phpunit" "not installed"
fi

printf '\n%b\n' "${SUCCESS}✓ code-quality-assurance: ${RAN} step(s) passed, ${SKIPPED} skipped${NC}"
