<?php

declare(strict_types=1);

namespace Core\Cache;

class Cache
{
    /**
     * @var string
     */
    protected string $cachePath;
    /**
     * @var int
     */
    protected int $defaultTtl;

    /**
     * @param string $cachePath
     * @param int $defaultTtl
     */
    public function __construct(string $cachePath = 'storage/cache', int $defaultTtl = 3600)
    {
        $this->cachePath = rtrim($cachePath, '/');
        $this->defaultTtl = $defaultTtl;

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int|null $ttl
     * @return bool
     */
    public function set(string $key, mixed $data, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $cacheFile = $this->getFileName($key);
        $cacheData = [
            'expires' => time() + $ttl,
            'data' => $data
        ];
        return file_put_contents($cacheFile, serialize($cacheData)) !== false;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $cacheFile = $this->getFileName($key);

        if (!file_exists($cacheFile)) {
            return null;
        }

        $content = file_get_contents($cacheFile);
        if ($content === false) {
            return null;
        }

        $cacheData = @unserialize($content);
        if (!is_array($cacheData) || !isset($cacheData['expires'], $cacheData['data'])) {
            unlink($cacheFile);
            return null;
        }

        if (!is_int($cacheData['expires']) || $cacheData['expires'] < time()) {
            unlink($cacheFile);
            return null;
        }

        return $cacheData['data'];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $cacheFile = $this->getFileName($key);
        return file_exists($cacheFile);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $cacheFile = $this->getFileName($key);
        return file_exists($cacheFile) && unlink($cacheFile);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $files = glob($this->cachePath . '/*.cache');
        if (!is_array($files)) {
            return;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getFileName(string $key): string
    {
        return $this->cachePath . '/' . sha1($key) . '.cache';
    }

    /**
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    /**
     * @param string $cachePath
     * @return void
     */
    public function setCachePath(string $cachePath): void
    {
        $this->cachePath = $cachePath;
    }

    /**
     * @return int
     */
    public function getDefaultTtl(): int
    {
        return $this->defaultTtl;
    }

    /**
     * @param int $defaultTtl
     * @return void
     */
    public function setDefaultTtl(int $defaultTtl): void
    {
        $this->defaultTtl = $defaultTtl;
    }
}