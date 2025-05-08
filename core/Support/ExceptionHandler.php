<?php

namespace Core\Support;

class ExceptionHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleException(\Throwable $e): void
    {
        http_response_code(500);

        $isDev = getenv('APP_ENV') !== 'production';

        if ($isDev) {
            echo "<h1>Exception: " . get_class($e) . "</h1>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . " (Line " . $e->getLine() . ")</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            error_log($e); // log into Apache error log
            echo "Something went wrong.";
        }
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
