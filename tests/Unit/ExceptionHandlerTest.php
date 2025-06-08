<?php

namespace Unit;

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