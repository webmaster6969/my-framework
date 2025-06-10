<?php

namespace Unit;

use Core\Storage\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var string
     */
    private string $tmpFilePath;

    /**
     * @var File
     */
    private File $file;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'file_test_');
        if ($tmpPath === false) {
            $this->fail('Не удалось создать временный файл');
        }
        $this->tmpFilePath = $tmpPath;

        file_put_contents($this->tmpFilePath, 'test content');

        $size = filesize($this->tmpFilePath);
        if ($size === false) {
            $this->fail('Не удалось определить размер временного файла');
        }

        $this->file = new File(
            'example.test.txt',
            $this->tmpFilePath,
            'text/plain',
            $size
        );
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if (isset($this->tmpFilePath) && file_exists($this->tmpFilePath)) {
            unlink($this->tmpFilePath);
        }
    }

    /**
     * @return void
     */
    public function testGetExtension(): void
    {
        $this->assertEquals('txt', $this->file->getExtension());
    }

    /**
     * @return void
     */
    public function testGetFilenameWithoutExtension(): void
    {
        $this->assertEquals('example.test', $this->file->getFilenameWithoutExtension());
    }

    /**
     * @return void
     */
    public function testGetMimeType(): void
    {
        $this->assertEquals('text/plain', $this->file->getMimeType());
    }

    /**
     * @return void
     */
    public function testGetSize(): void
    {
        $this->assertEquals(strlen('test content'), $this->file->getSize());
    }

    /**
     * @return void
     */
    public function testPath(): void
    {
        $this->assertEquals($this->tmpFilePath, $this->file->path());
    }

    /**
     * @return void
     */
    public function testMove(): void
    {
        $destination = sys_get_temp_dir() . '/moved_test_file.txt';
        if (file_exists($destination)) {
            unlink($destination);
        }

        $result = $this->file->move($destination);

        $this->assertTrue($result);
        $this->assertFileExists($destination);

        $this->assertEquals($destination, $this->file->path());

        unlink($destination);
    }

    /**
     * @return void
     */
    public function testDelete(): void
    {
        $this->assertFileExists($this->tmpFilePath);
        $this->assertTrue($this->file->delete());
        $this->assertFileDoesNotExist($this->tmpFilePath);
    }
}