<?php

declare(strict_types=1);

namespace Core\Support\Exception;

use Core\Logger\Logger;
use ErrorException;

class ExceptionHandler
{
    /**
     * @return void
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    /**
     * @param \Throwable $e
     * @return void
     */
    public static function handleException(\Throwable $e): void
    {
        http_response_code(500);

        $isDev = getenv('APP_ENV') !== 'production';

        Logger::error($e->getMessage());

        if ($isDev) {
            echo "<h1>Exception: " . get_class($e) . "</h1>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . " (Line " . $e->getLine() . ")</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            echo "Something went wrong.";
        }
    }

    /**
     * @throws ErrorException
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
