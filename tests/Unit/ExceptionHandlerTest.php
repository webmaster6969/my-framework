<?php

namespace Tests\Unit;

use Core\Support\Exception\ExceptionHandler;
use ErrorException;
use Exception;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('APP_ENV=development');
    }

    public function testHandleErrorThrowsErrorException(): void
    {
        $this->expectException(ErrorException::class);
        ExceptionHandler::handleError(E_USER_WARNING, 'Test warning', 'test.php', 123);
    }

    public function testHandleExceptionInProductionOutputsGenericMessage(): void
    {
        putenv('APP_ENV=production');

        ob_start();
        ExceptionHandler::handleException(new Exception("Test message"));
        $output = ob_get_clean();

        $this->assertSame('Something went wrong.', trim($output));
    }
}