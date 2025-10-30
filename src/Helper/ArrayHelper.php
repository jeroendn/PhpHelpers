<?php
declare(strict_types=1);

namespace jeroendn\PhpHelpers\Helper;

class ArrayHelper
{
    /**
     * Sort an array of objects by a property of the object.
     * Does not work on multidimensional arrays.
     * @param array  $array
     * @param string $property
     * @param bool   $asc
     * @return void
     */
    public static function sortByProperty(array &$array, string $property, bool $asc = true): void
    {
        if ($asc) {
            usort($array, function ($a, $b) use ($property) {
                return $a->$property <=> $b->$property;
            });
        }
        else {
            usort($array, function ($a, $b) use ($property) {
                return $b->$property <=> $a->$property;
            });
        }
    }

    /**
     * Sort an array of objects by multiple properties of the object.
     * Does not work on multidimensional arrays.
     * @param array    $array
     * @param string[] $properties
     * @param bool     $asc
     * @return void
     */
    public static function sortByProperties(array &$array, array $properties, bool $asc = true): void
    {
        foreach ($properties as $property) {
            if (!is_string($property)) {
                continue; // Do not allow non-string properties
            }

            self::sortByProperty($array, $property, $asc);
        }
    }
}
