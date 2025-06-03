<?php

declare(strict_types=1);

namespace Core\Support\App;

class App
{
    /**
     * @var App
     */
    protected static App $app;
    /**
     * @var string
     */
    protected string $basePath;

    /**
     * @param string $basePath
     * @return void
     */
    public static function init(string $basePath): void
    {
        self::$app = new App();
        self::$app->basePath = $basePath;
    }

    /**
     * @return string
     */
    public static function getBasePath(): string
    {
        return self::$app->basePath;
    }

}