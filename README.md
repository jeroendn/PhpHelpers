# PhpHelpers
A package containing some useful functions within classes.

## Installation
```shell
composer require jeroendn/php-helpers
```

## How to use
```php
use jeroendn\PhpHelpers\ArrayHelper;

$array = [
    (new stdClass)->name = 'John',
    (new stdClass)->name = 'Hans',
    (new stdClass)->name = 'Piet',
]

ArrayHelper::sortArrayByProperty($array, 'name', true);

echo $array[0]->name; // Hans
```

## Development
### Run tests
```shell
./vendor/bin/phpunit
```