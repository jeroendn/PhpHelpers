<?php

namespace jeroendn\PhpHelpers;

class ArrayHelper
{
  /**
   * Sort an array of objects by a property of the object.
   * Does not work on multidimensional arrays.
   * @param array $array
   * @param string $property
   * @param bool $asc
   * @return void
   */
  public static function sortArrayByProperty(array &$array, string $property, bool $asc = true): void
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
   * @param array $array
   * @param string[] $properties
   * @param bool $asc
   * @return void
   */
  public static function sortArrayByProperties(array &$array, array $properties, bool $asc = true): void
  {
    foreach ($properties as $index => $property) {
      if (!is_string($property)) {
        continue; // Do not allow non-string properties
      }

      self::sortArrayByProperty($array, $property, $asc);
    }
  }
}