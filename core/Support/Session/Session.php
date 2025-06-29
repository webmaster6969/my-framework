<?php

declare(strict_types=1);

namespace Core\Support\Session;

class Session
{
    /**
     * @var array<string, mixed>
     */
    private static ?array $errors = null;

    /**
     * @var array<string, mixed>
     */
    private static ?array $flash = null;

    /**
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::$errors = is_array($_SESSION['_errors'] ?? null)
            ? self::stringifyKeys($_SESSION['_errors'])
            : [];

        self::$flash = is_array($_SESSION['_flash'] ?? null)
            ? self::stringifyKeys($_SESSION['_flash'])
            : [];

        $_SESSION['_errors'] = [];
        $_SESSION['_flash'] = [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return self::stringifyKeys($_SESSION);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * @param string $key
     * @return void
     */
    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * @return void
     */
    public static function destroy(): void
    {
        session_destroy();
    }

    /**
     * @return void
     */
    public static function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function error(): ?array
    {
        return self::$errors;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function flash(string $key): mixed
    {
        return self::$flash[$key] ?? null;
    }

    /**
     * @param array<array-key, mixed> $array
     * @return array<string, mixed>
     */
    private static function stringifyKeys(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[(string)$key] = $value;
        }
        return $result;
    }
}