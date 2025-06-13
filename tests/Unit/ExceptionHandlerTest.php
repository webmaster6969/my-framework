<?php

declare(strict_types=1);

namespace Unit;

use Core\Logger\Logger;
use Core\Support\Exception\ExceptionHandler;
use ErrorException;
use Exception;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        putenv('APP_ENV=development');
        Logger::setLogFile(__DIR__ . '/../../logs/logs_test.log');
        Logger::info('Test log');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        putenv('APP_ENV=development');
        if (file_exists(__DIR__ . '/../../logs/logs_test.log')) {
            unlink(__DIR__ . '/../../logs/logs_test.log');
        }
    }

    /**
     * @return void
     * @throws ErrorException
     */
    public function testHandleErrorThrowsErrorException(): void
    {
        $this->expectException(ErrorException::class);
        ExceptionHandler::handleError(E_USER_WARNING, 'Test warning', 'index.php', 123);
    }

    /**
     * @return void
     */
    public function testHandleExceptionInProductionOutputsGenericMessage(): void
    {
        putenv('APP_ENV=production');

        ob_start();
        ExceptionHandler::handleException(new Exception("Test message"));
        $output = ob_get_clean();

        $this->assertNotFalse($output);
        $this->assertSame('Something went wrong.', trim($output));
    }
}