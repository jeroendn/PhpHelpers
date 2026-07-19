<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return new Config()
    ->setRiskyAllowed(false)
    ->setCacheFile(__DIR__ . '/tmp/cs-fixer')
    ->setRules([
        '@auto' => true, // @PER-CS + PHP migration level from composer.json
    ])
    ->setFinder(
        new Finder()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
            ->name('*.php')
            ->append([
                __FILE__, // Include this config file
                __DIR__ . '/rector.php',
            ]),
    );
