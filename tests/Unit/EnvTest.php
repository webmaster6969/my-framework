<?php

namespace Tests\Unit;

use Core\Support\Env\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testEnv()
    {
        Env::load(__DIR__ . '/../../.env-test');
        $this->assertEquals('MyFrameworkTest', getenv('APP_NAME'));
    }
}