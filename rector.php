<?php

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withCache(__DIR__ . '/tmp/rector', FileCacheStorage::class)
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/rector.php',
        __DIR__ . '/.php-cs-fixer.dist.php',
    ])
    ->withPhpSets();
