<?php

declare(strict_types=1);

namespace Core\Support\Session;

class Session
{
    private static array $errors;
    private static array $flash;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        static::$errors = $_SESSION['_errors'] ?? [];
        static::$flash = $_SESSION['_flash'] ?? [];

        if (isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        if (isset($_SESSION['_errors'])) {
            $_SESSION['_errors'] = [];
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function all(): array
    {
        return $_SESSION;
    }

    public static function clear(): void
    {
        $_SESSION = [];
    }

    public static function error(): mixed
    {
        if (isset(static::$errors)) {
            return static::$errors;
        }

        return null;
    }

    public static function flash(string $key): mixed
    {
        if (isset(static::$flash[$key])) {
            return static::$flash[$key];
        }

        return null;
    }
}