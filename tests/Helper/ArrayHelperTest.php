<?php

declare(strict_types=1);

namespace Helper;

use jeroendn\PhpHelpers\Helper\ArrayHelper;
use PHPUnit\Framework\TestCase;

final class ArrayHelperTest extends TestCase
{
    public function testSortObjectsByPropertySortsAscendingByDefault(): void
    {
        $array = [
            (object) ['age' => 30],
            (object) ['age' => 10],
            (object) ['age' => 20],
        ];

        ArrayHelper::sortObjectsByProperty($array, 'age');

        self::assertSame([10, 20, 30], array_column($array, 'age'));
    }

    public function testSortObjectsByPropertySortsDescending(): void
    {
        $array = [
            (object) ['age' => 10],
            (object) ['age' => 30],
            (object) ['age' => 20],
        ];

        ArrayHelper::sortObjectsByProperty($array, 'age', false);

        self::assertSame([30, 20, 10], array_column($array, 'age'));
    }

    public function testSortObjectsByPropertySortsStrings(): void
    {
        $array = [
            (object) ['name' => 'Charlie'],
            (object) ['name' => 'Alice'],
            (object) ['name' => 'Bob'],
        ];

        ArrayHelper::sortObjectsByProperty($array, 'name');

        self::assertSame(['Alice', 'Bob', 'Charlie'], array_column($array, 'name'));
    }

    public function testSortObjectsByPropertyKeepsEqualValuesInOriginalOrder(): void
    {
        $first  = (object) ['age' => 20, 'name' => 'Alice'];
        $second = (object) ['age' => 20, 'name' => 'Bob'];

        $array = [
            (object) ['age' => 30, 'name' => 'Charlie'],
            $first,
            $second,
        ];

        ArrayHelper::sortObjectsByProperty($array, 'age');

        self::assertSame([$first, $second], [$array[0], $array[1]]);
    }

    public function testSortObjectsByPropertyHandlesEmptyArray(): void
    {
        $array = [];

        ArrayHelper::sortObjectsByProperty($array, 'age');

        self::assertSame([], $array);
    }

    public function testSortObjectsByPropertiesUsesLastPropertyAsPrimarySortKey(): void
    {
        $array = [
            (object) ['name' => 'Bob', 'age' => 20],
            (object) ['name' => 'Alice', 'age' => 30],
            (object) ['name' => 'Charlie', 'age' => 20],
        ];

        ArrayHelper::sortObjectsByProperties($array, ['name', 'age']);

        // Each property triggers a full stable sort, so the last property ends up
        // as the primary sort key, and earlier properties only break ties.
        self::assertSame(['Bob', 'Charlie', 'Alice'], array_column($array, 'name'));
        self::assertSame([20, 20, 30], array_column($array, 'age'));
    }

    public function testSortObjectsByPropertiesSortsDescending(): void
    {
        $array = [
            (object) ['name' => 'Bob', 'age' => 20],
            (object) ['name' => 'Alice', 'age' => 30],
            (object) ['name' => 'Charlie', 'age' => 20],
        ];

        ArrayHelper::sortObjectsByProperties($array, ['name', 'age'], false);

        self::assertSame(['Alice', 'Charlie', 'Bob'], array_column($array, 'name'));
        self::assertSame([30, 20, 20], array_column($array, 'age'));
    }

    public function testSortObjectsByPropertiesSkipsNonStringProperties(): void
    {
        $array = [
            (object) ['age' => 30],
            (object) ['age' => 10],
        ];

        ArrayHelper::sortObjectsByProperties($array, [null, 123, 'age']);

        self::assertSame([10, 30], array_column($array, 'age'));
    }

    public function testSortObjectsByPropertiesWithoutStringPropertiesLeavesOrderUntouched(): void
    {
        $array = [
            (object) ['age' => 30],
            (object) ['age' => 10],
        ];

        ArrayHelper::sortObjectsByProperties($array, [null, 123]);

        self::assertSame([30, 10], array_column($array, 'age'));
    }
}
