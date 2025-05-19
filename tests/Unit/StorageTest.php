<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use Core\Storage\Storage;
use Exception;

class StorageTest extends TestCase
{
    protected string $testRoot;

    protected function setUp(): void
    {
        $this->testRoot = __DIR__ . '/temp_storage';

        if (!is_dir($this->testRoot)) {
            mkdir($this->testRoot, 0777, true);
        }

        // Можно не использовать init() — настраиваем диск вручную
        $storage = new Storage($this->testRoot);

        $ref = new \ReflectionClass(Storage::class);
        $prop = $ref->getProperty('disk');
        $prop->setValue(null, $storage); // FIX: для PHP 8.3+
    }

    protected function tearDown(): void
    {
        $this->deleteDir($this->testRoot);
    }

    protected function deleteDir($dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->deleteDir($path) : unlink($path);
        }

        rmdir($dir);
    }

    public function testPutCreatesFile(): void
    {
        $storage = new Storage($this->testRoot);
        $result = $storage->put('test.txt', 'Hello, world!');

        $this->assertTrue($result);
        $this->assertFileExists($this->testRoot . '/test.txt');
    }

    public function testGetReturnsContents(): void
    {
        $path = $this->testRoot . '/sample.txt';
        file_put_contents($path, 'Sample content');

        $storage = new Storage($this->testRoot);
        $this->assertEquals('Sample content', $storage->get('sample.txt'));
    }

    public function testGetThrowsExceptionIfFileNotExists(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File [missing.txt] does not exist.');

        $storage = new Storage($this->testRoot);
        $storage->get('missing.txt');
    }

    public function testExistsReturnsTrueIfFileExists(): void
    {
        file_put_contents($this->testRoot . '/exists.txt', '123');
        $storage = new Storage($this->testRoot);
        $this->assertTrue($storage->exists('exists.txt'));
    }

    public function testExistsReturnsFalseIfFileNotExists(): void
    {
        $storage = new Storage($this->testRoot);
        $this->assertFalse($storage->exists('nope.txt'));
    }

    public function testDeleteRemovesFile(): void
    {
        $file = $this->testRoot . '/delete_me.txt';
        file_put_contents($file, 'data');

        $storage = new Storage($this->testRoot);
        $this->assertTrue($storage->delete('delete_me.txt'));
        $this->assertFileDoesNotExist($file);
    }

    public function testDeleteReturnsFalseIfFileDoesNotExist(): void
    {
        $storage = new Storage($this->testRoot);
        $this->assertFalse($storage->delete('nonexistent.txt'));
    }
}