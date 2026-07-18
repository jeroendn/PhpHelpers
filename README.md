# PhpHelpers

A package containing some useful functions within classes.

## Installation

```shell
composer require jeroendn/php-helpers
```

## Contents

### Classes

```php
use jeroendn\PhpHelpers\Helper\ArrayHelper;

$array = [
    (new stdClass)->name = 'John',
    (new stdClass)->name = 'Hans',
    (new stdClass)->name = 'Piet',
]

ArrayHelper::sortByProperty($array, 'name', true);

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
docker exec -it php_phphelpers ./vendor/bin/phpunit tests
```