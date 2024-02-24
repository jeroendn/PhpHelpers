<?php

namespace jeroendn\PhpHelpers\Helper;

class Casing
{
    /**
     * @param string $string
     * @return string
     */
    public static function snakeToPascal(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * @param string $string
     * @return string
     */
    public static function snakeToCamel(string $string): string
    {
        return lcfirst(self::snakeToPascal($string));
    }

    /**
     * @param string $string
     * @return string
     */
    public static function toSnake(string $string): string
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }
}
