<?php

declare(strict_types=1);

namespace Core\Storage;

use Core\Support\App\App;
use Exception;

class Storage
{
    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $disks;

    /**
     * @var string
     */
    protected string $defaultDisk;

    /**
     * @var Storage
     */
    protected static Storage $disk;

    /**
     * @var string
     */
    protected string $root;

    /**
     * @param string $directory
     * @param array{disks: array<string, array<string, mixed>>, default: string} $config
     */
    public static function init(string $directory, array $config): void
    {
        $disk = new Storage($directory);
        $disk->disks = $config['disks'];
        $disk->defaultDisk = $config['default'];
        static::$disk = $disk;
    }

    /**
     * @param string $root
     */
    public function __construct(string $root)
    {
        $this->root = rtrim($root, '/');
    }

    /**
     * @param File $file
     * @return string
     */
    protected static function path(File $file): string
    {
        return static::$disk->root . '/' . ltrim($file->path(), '/');
    }

    /**
     * @param File $file
     * @param string $contents
     * @return bool
     */
    public function put(File $file, string $contents): bool
    {
        $path = static::$disk->path($file);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return file_put_contents($path, $contents) !== false;
    }

    /**
     * @param File $file
     * @return string|false
     * @throws Exception
     */
    public function getContents(File $file): string|false
    {
        $path = static::$disk->path($file);
        if (!file_exists($path)) {
            throw new Exception("File [" . $path . "] does not exist.");
        }

        return file_get_contents($path);
    }

    /**
     * @param File $file
     * @return bool
     */
    public function exists(File $file): bool
    {
        return file_exists($this->path($file));
    }

    /**
     * @param File $file
     * @return bool
     */
    public function delete(File $file): bool
    {
        $path = $this->path($file);
        return file_exists($path) && unlink($path);
    }

    /**
     * @param IFile $source
     * @param string $destination
     * @return bool
     */
    public function move(IFile $source, string $destination): bool
    {
        return $source->move($this->root . '/' . trim($destination, '/') . '/' . $source->getClientOriginalName());
    }
}