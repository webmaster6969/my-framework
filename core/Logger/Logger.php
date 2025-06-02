<?php

namespace Core\Logger;

class Logger
{
    protected static string $logFile = '/../logs/logs.log';

    public static function setLogFile(string $file): void
    {
        self::$logFile = $file;
    }

    public static function info(string $message): void
    {
        self::writeLog('INFO', $message);
    }

    public static function warning(string $message): void
    {
        self::writeLog('WARNING', $message);
    }

    public static function error(string $message): void
    {
        self::writeLog('ERROR', $message);
    }

    public static function debug(string $message): void
    {
        self::writeLog('DEBUG', $message);
    }

    protected static function writeLog(string $level, string $message): void
    {
        $date = date('Y-m-d H:i:s');
        $formatted = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $formatted, FILE_APPEND);
    }
}