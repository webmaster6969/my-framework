<?php

namespace Unit;

use PHPUnit\Framework\TestCase;
use Core\Cache\Cache;

class CacheTest extends TestCase
{
    /**
     * @var string
     */
    protected string $cachePath;
    /**
     * @var Cache
     */
    protected Cache $cache;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->cachePath = sys_get_temp_dir() . '/php-cache-test';
        if (is_dir($this->cachePath)) {
            $this->clearDirectory($this->cachePath);
        }
        $this->cache = new Cache($this->cachePath, 2);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->cache->clear();
        @rmdir($this->cachePath);
    }

    /**
     * @param string $dir
     * @return void
     */
    protected function clearDirectory(string $dir): void
    {
        $files = glob($dir . '/*.cache');

        if (is_array($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    /**
     * @return void
     */
    public function testSetAndGetCache(): void
    {
        $this->assertTrue($this->cache->set('test_key', 'test_value'));
        $this->assertEquals('test_value', $this->cache->get('test_key'));
    }

    /**
     * @return void
     */
    public function testHas(): void
    {
        $this->cache->set('foo', 'bar');
        $this->assertTrue($this->cache->has('foo'));
        $this->cache->delete('foo');
        $this->assertFalse($this->cache->has('foo'));
    }

    /**
     * @return void
     */
    public function testDelete(): void
    {
        $this->cache->set('to_delete', 'value');
        $this->assertTrue($this->cache->delete('to_delete'));
        $this->assertNull($this->cache->get('to_delete'));
    }

    /**
     * @return void
     */
    public function testClear(): void
    {
        $this->cache->set('a', 1);
        $this->cache->set('b', 2);
        $this->cache->clear();

        $this->assertNull($this->cache->get('a'));
        $this->assertNull($this->cache->get('b'));
    }

    /**
     * @return void
     */
    public function testExpiration(): void
    {
        $this->cache->set('expiring', 'value', 1); // 1 second TTL
        sleep(2);
        $this->assertNull($this->cache->get('expiring'));
    }

    /**
     * @return void
     */
    public function testCorruptedFileReturnsNull(): void
    {
        $file = $this->getCacheFileName('corrupted');
        file_put_contents($file, 'not a serialized string');
        $this->assertNull($this->cache->get('corrupted'));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getCacheFileName(string $key): string
    {
        return $this->cachePath . '/' . sha1($key) . '.cache';
    }

    /**
     * @return void
     */
    public function testSetAndGetWithCustomTTL(): void
    {
        $this->assertTrue($this->cache->set('ttl_test', 'value', 5));
        $this->assertEquals('value', $this->cache->get('ttl_test'));
    }

    /**
     * @return void
     */
    public function testGetCachePathAndTtl(): void
    {
        $this->assertEquals($this->cachePath, $this->cache->getCachePath());
        $this->assertEquals(2, $this->cache->getDefaultTtl());
    }

    /**
     * @return void
     */
    public function testSetCachePathAndTtl(): void
    {
        $newPath = $this->cachePath . '/new';
        $this->cache->setCachePath($newPath);
        $this->assertEquals($newPath, $this->cache->getCachePath());

        $this->cache->setDefaultTtl(100);
        $this->assertEquals(100, $this->cache->getDefaultTtl());
    }
}