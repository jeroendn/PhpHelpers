<?php

declare(strict_types=1);

namespace Helper;

use jeroendn\PhpHelpers\Helper\Casing;
use PHPUnit\Framework\TestCase;

final class CasingTest extends TestCase
{
    public function testSnakeToPascalConvertsSnakeCase(): void
    {
        self::assertSame('FooBarBaz', Casing::snakeToPascal('foo_bar_baz'));
    }

    public function testSnakeToPascalHandlesSingleWord(): void
    {
        self::assertSame('Foo', Casing::snakeToPascal('foo'));
    }

    public function testSnakeToPascalHandlesEmptyString(): void
    {
        self::assertSame('', Casing::snakeToPascal(''));
    }

    public function testSnakeToPascalKeepsDigits(): void
    {
        self::assertSame('FooBar1', Casing::snakeToPascal('foo_bar1'));
    }

    public function testSnakeToCamelConvertsSnakeCase(): void
    {
        self::assertSame('fooBarBaz', Casing::snakeToCamel('foo_bar_baz'));
    }

    public function testSnakeToCamelHandlesSingleWord(): void
    {
        self::assertSame('foo', Casing::snakeToCamel('foo'));
    }

    public function testToSnakeConvertsPascalCase(): void
    {
        self::assertSame('foo_bar_baz', Casing::toSnake('FooBarBaz'));
    }

    public function testToSnakeConvertsCamelCase(): void
    {
        self::assertSame('foo_bar_baz', Casing::toSnake('fooBarBaz'));
    }

    public function testToSnakeLeavesSnakeCaseUntouched(): void
    {
        self::assertSame('foo_bar', Casing::toSnake('foo_bar'));
    }

    public function testToSnakeSeparatesDigitFromFollowingUppercase(): void
    {
        self::assertSame('foo1_bar', Casing::toSnake('foo1Bar'));
    }

    public function testToSnakeKeepsAcronymTogether(): void
    {
        self::assertSame('http_response', Casing::toSnake('HTTPResponse'));
    }
}
