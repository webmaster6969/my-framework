<?php

namespace Unit;

use PHPUnit\Framework\TestCase;
use Core\Logger\Logger;

class LoggerTest extends TestCase
{
    /**
     * @var string
     */
    private string $tempLogFile;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->tempLogFile = sys_get_temp_dir() . '/test_logger.log';
        if (file_exists($this->tempLogFile)) {
            unlink($this->tempLogFile);
        }

        Logger::setLogFile($this->tempLogFile);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempLogFile)) {
            unlink($this->tempLogFile);
        }
    }

    /**
     * @return void
     */
    public function testInfoLogIsWritten(): void
    {
        Logger::info('Test info message');
        $this->assertLogContains('INFO', 'Test info message');
    }

    /**
     * @return void
     */
    public function testWarningLogIsWritten(): void
    {
        Logger::warning('Test warning message');
        $this->assertLogContains('WARNING', 'Test warning message');
    }

    /**
     * @return void
     */
    public function testErrorLogIsWritten(): void
    {
        Logger::error('Test error message');
        $this->assertLogContains('ERROR', 'Test error message');
    }

    /**
     * @return void
     */
    public function testDebugLogIsWritten(): void
    {
        Logger::debug('Test debug message');
        $this->assertLogContains('DEBUG', 'Test debug message');
    }

    /**
     * @param string $level
     * @param string $message
     * @return void
     */
    private function assertLogContains(string $level, string $message): void
    {
        $this->assertFileExists($this->tempLogFile);
        $content = file_get_contents($this->tempLogFile);
        $this->assertIsString($content, 'Log file could not be read.');
        $this->assertStringContainsString("[$level] $message", $content);
    }
}