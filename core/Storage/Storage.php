<?php

namespace Core\Storage;

use Exception;

class Storage
{
    protected array $disks;
    protected string $defaultDisk;
    protected static Storage $disk;
    protected string $root;

    public static function init(): void
    {
        $disk = new Storage('/../../storage');
        $config = require __DIR__ . '/../../config/filesystems.php';
        $disk->disks = $config['disks'];
        $disk->defaultDisk = $config['default'];
        static::$disk = $disk;
    }

    public function __construct($root)
    {
        $this->root = rtrim($root, '/');
    }

    protected static function path($file): string
    {
        return static::$disk->root . '/' . ltrim($file, '/');
    }

    public function put($file, $contents): bool
    {
        $path = static::$disk->path($file);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return file_put_contents($path, $contents) !== false;
    }

    /**
     * @throws Exception
     */
    public function get($file): false|string
    {
        $path = static::$disk->path($file);
        if (!file_exists($path)) {
            throw new Exception("File [$file] does not exist.");
        }
        return file_get_contents($path);
    }

    public function exists($file): bool
    {
        return file_exists($this->path($file));
    }

    public function delete($file)
    {
        $path = $this->path($file);
        return file_exists($path) ? unlink($path) : false;
    }
}