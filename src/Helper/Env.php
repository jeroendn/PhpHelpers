<?php

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
     * Returns the environment variables form .env file.
     * @param string $filename
     * @return array
     */
    public static function getVars(string $filename = '.env'): array
    {
        return parse_ini_file($filename) ?? [];
    }
}
