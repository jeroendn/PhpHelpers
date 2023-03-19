<?php

namespace jeroendn\PhpHelpers;

class DebugHelper
{
    /**
     * Prints a variable to the screen.
     * @param $variable
     * @return void
     */
    public static function d($variable): void
    {
        echo '<pre>' . print_r($variable, true) . '</pre>';
    }

    /**
     * Prints a variable to the screen and stops execution of script.
     * @param $variable
     * @return void
     */
    public static function dd($variable): void
    {
        echo '<pre>' . print_r($variable, true) . '</pre>';
        die;
    }
}