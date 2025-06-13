<?php

namespace Core\Logger;

use Core\Support\App\App;

class Logger
{
    /**
     * @var string
     */
    protected static string $logFile = 'logs/logs.log';

    /**
     * @param string $file
     * @return void
     */
    public static function setLogFile(string $file): void
    {
        self::$logFile = $file;
    }

    /**
     * @param string $message
     * @return void
     */
    public static function info(string $message): void
    {
        self::writeLog('INFO', $message);
    }

    /**
     * @param string $message
     * @return void
     */
    public static function warning(string $message): void
    {
        self::writeLog('WARNING', $message);
    }

    /**
     * @param string $message
     * @return void
     */
    public static function error(string $message): void
    {
        self::writeLog('ERROR', $message);
    }

    /**
     * @param string $message
     * @return void
     */
    public static function debug(string $message): void
    {
        self::writeLog('DEBUG', $message);
    }

    /**
     * @param string $level
     * @param string $message
     * @return void
     */
    protected static function writeLog(string $level, string $message): void
    {
        $date = date('Y-m-d H:i:s');
        $formatted = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $formatted, FILE_APPEND);
    }
}