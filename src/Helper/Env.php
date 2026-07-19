<?php

declare(strict_types=1);

namespace jeroendn\PhpHelpers\Helper;

class Env
{
    /**
     * Loads the .env variables to the server environment. Accessible via getenv('EXAMPLE')
     * @param string $filename Overwrite filename
     * @return void
     */
    public static function load(string $filename = '.env'): void
    {
        $env = self::getVars($filename);

        foreach ($env as $key => $value) {
            putenv("$key=$value");
        }
    }

    /**
     * Returns the environment variables from the .env file.
     * Returns an empty array when the file is missing or cannot be parsed.
     * @param string $filename
     * @return array
     */
    public static function getVars(string $filename = '.env'): array
    {
        $vars = @parse_ini_file($filename);

        return $vars === false ? [] : $vars;
    }
}
