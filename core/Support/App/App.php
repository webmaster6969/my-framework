<?php

namespace Core\Support\App;

class App
{
    protected static $app;
    protected string $basePath;
    public static function init($basePath): void
    {
        self::$app = new App();
        self::$app->basePath = $basePath;
    }

    public static function getBasePath()
    {
        return self::$app->basePath;
    }

}