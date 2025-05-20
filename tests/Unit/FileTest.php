<?php

namespace Tests\Unit;

use Core\Storage\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private string $tmpFilePath;
    private File $file;

    protected function setUp(): void
    {
        // Создаем временный файл
        $this->tmpFilePath = tempnam(sys_get_temp_dir(), 'file_test_');
        file_put_contents($this->tmpFilePath, 'test content');

        $this->file = new File(
            'example.test.txt',
            $this->tmpFilePath,
            'text/plain',
            filesize($this->tmpFilePath)
        );
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmpFilePath)) {
            unlink($this->tmpFilePath);
        }
    }

    public function testGetExtension(): void
    {
        $this->assertEquals('txt', $this->file->getExtension());
    }

    public function testGetFilenameWithoutExtension(): void
    {
        $this->assertEquals('example.test', $this->file->getFilenameWithoutExtension());
    }

    public function testGetMimeType(): void
    {
        $this->assertEquals('text/plain', $this->file->getMimeType());
    }

    public function testGetSize(): void
    {
        $this->assertEquals(strlen('test content'), $this->file->getSize());
    }

    public function testPath(): void
    {
        $this->assertEquals($this->tmpFilePath, $this->file->path());
    }

    public function testMove(): void
    {
        $destination = sys_get_temp_dir() . '/moved_test_file.txt';
        if (file_exists($destination)) {
            unlink($destination);
        }

        $result = $this->file->move($destination);

        $this->assertTrue($result);
        $this->assertFileExists($destination);

        // Внутренний путь должен обновиться
        $this->assertEquals($destination, $this->file->path());

        unlink($destination);
    }

    public function testDelete(): void
    {
        $this->assertFileExists($this->tmpFilePath);
        $this->assertTrue($this->file->delete());
        $this->assertFileDoesNotExist($this->tmpFilePath);
    }
}
