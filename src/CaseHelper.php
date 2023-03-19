<?php

namespace jeroendn\PhpHelpers;

class CaseHelper
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
        return lcfirst(self::snakeToCamel($string));
    }
}