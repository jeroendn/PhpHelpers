<?php
declare(strict_types=1);

namespace jeroendn\PhpHelpers\Helper;

class Debug
{
    /**
     * Prints a variable to the screen.
     * @param mixed $variable
     * @return void
     */
    public static function raw(mixed $variable): void
    {
        echo '<pre>' . print_r($variable, true) . '</pre>';
    }

    /**
     * Prints a variable to the screen and stops execution of script.
     * @param mixed ...$vars
     * @return void
     */
    public static function d(mixed ...$vars): void
    {
        dump($vars);
    }

    /**
     * Prints a variable to the screen and stops execution of script.
     * @param mixed ...$vars
     * @return never
     */
    public static function dd(mixed ...$vars): never
    {
        dd($vars);
    }
}
