# PhpHelpers

PHP helper classes and quality-tooling scripts to use across multiple projects.

## Installation

```shell
composer require jeroendn/php-helpers
```

## Contents

### Classes

| Class                 | Purpose                                                       |
|-----------------------|---------------------------------------------------------------|
| `Helper\ArrayHelper`  | Sort arrays of objects by one or more properties              |
| `Helper\Casing`       | Convert strings between snake_case, camelCase and PascalCase  |
| `Helper\Debug`        | Dump variables (`raw`, `d`, `dd`)                             |
| `Helper\Env`          | Load `.env` variables into the server environment             |
| `Wrapper\DateTime`    | DateTime wrapper that can freeze into an immutable and back   |

```php
use jeroendn\PhpHelpers\Helper\ArrayHelper;

$array = [
    (object) ['name' => 'John'],
    (object) ['name' => 'Hans'],
    (object) ['name' => 'Piet'],
];

ArrayHelper::sortObjectsByProperty($array, 'name');

echo $array[0]->name; // Hans
```

### Scripts

#### Code quality assurance

This package ships a whole-project quality gate to run before opening a PR. Composer links it into `vendor/bin` of every
project requiring this package:

```shell
vendor/bin/code-quality-assurance.sh
```

It runs composer normalize, composer validate, rector, php-cs-fixer, phpstan and phpunit — in that order, aborting on
the first failure. Each step only runs when its tool and configuration are present in the project the script is invoked
in, so the same script serves projects with different setups.

## Development

Day-to-day commands run inside the `php_phphelpers` container through the `./develop` wrapper (`develop.cmd` makes the
same commands work from PowerShell/cmd on Windows):

```shell
./develop up -d --build  # build + start the container
./develop install        # composer install
./develop cqa            # run the code quality assurance gate
./develop help           # list all commands
```

### Run tests

```shell
./develop composer phpunit
```
