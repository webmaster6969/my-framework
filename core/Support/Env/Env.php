<?php

declare(strict_types=1);

namespace Core\Support\Env;

use Exception;

class Env
{
    /**
     * @throws Exception
     */
    public static function load(string $path = '.env'): void
    {
        if (!file_exists($path)) {
            throw new Exception(".env file not found at: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new Exception("Unable to read .env file at: {$path}");
        }

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            $value = trim($value, "\"'");

            if (!array_key_exists($name, $_ENV)) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
