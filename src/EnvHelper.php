<?php

namespace jeroendn\PhpHelpers;

class EnvHelper
{
    /**
     * Loads the .env variables to the server environment. Accessible via getenv('EXAMPLE')
     * @param string $filename Overwrite filename
     * @return void
     */
    public static function loadEnv(string $filename = '.env'): void
    {
        $env = self::getEnvVars($filename);

        foreach ($env as $key => $value) {
            putenv("$key=$value");
        }
    }

    /**
     * Returns the environment variables form .env file.
     * @param string $filename
     * @return array
     */
    public static function getEnvVars(string $filename = '.env'): array
    {
        return parse_ini_file($filename) ?? [];
    }
}