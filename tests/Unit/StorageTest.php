<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Storage\Storage;
use Exception;
use Core\Storage\File;
use Core\Storage\IFile;
use ReflectionClass;

class StorageTest extends TestCase
{
    private string $tempDir;
    private Storage $storage;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/storage_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
        $this->storage = new Storage($this->tempDir);
        Storage::init(); // можно инициализировать тут, если нужны настройки
        $this->overrideStaticDisk($this->storage);
    }

    protected function tearDown(): void
    {
        $this->deleteDir($this->tempDir);
    }

    private function overrideStaticDisk(Storage $disk): void
    {
        $reflection = new ReflectionClass(Storage::class);
        $property = $reflection->getProperty('disk');
        $property->setValue(null, $disk);
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = "$dir/$file";
            if (is_dir($path)) {
                $this->deleteDir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    public function testPutAndGetContents(): void
    {
        $file = $this->createMock(File::class);
        $file->method('path')->willReturn('test/file.txt');

        $result = $this->storage->put($file, 'Hello World');
        $this->assertTrue($result);

        $contents = $this->storage->getContents($file);
        $this->assertEquals('Hello World', $contents);
    }

    public function testExists(): void
    {
        $file = $this->createMock(File::class);
        $file->method('path')->willReturn('exists.txt');

        $this->storage->put($file, 'test');
        $this->assertTrue($this->storage->exists($file));
    }

    public function testDelete(): void
    {
        $file = $this->createMock(File::class);
        $file->method('path')->willReturn('to_delete.txt');

        $this->storage->put($file, 'remove me');
        $this->assertTrue($this->storage->delete($file));
        $this->assertFalse($this->storage->exists($file));
    }

    public function testGetContentsThrowsException(): void
    {
        $this->expectException(Exception::class);
        $file = $this->createMock(File::class);
        $file->method('path')->willReturn('nonexistent.txt');
        $this->storage->getContents($file);
    }

    public function testMove(): void
    {
        $sourceMock = $this->createMock(IFile::class);
        $sourceMock->method('getClientOriginalName')->willReturn('file.jpg');
        $sourceMock->expects($this->once())
            ->method('move')
            ->with($this->stringEndsWith('/destination/file.jpg'))
            ->willReturn(true);

        $this->assertTrue($this->storage->move($sourceMock, 'destination'));
    }
}