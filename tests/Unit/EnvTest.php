<?php

declare(strict_types=1);

namespace Unit;

use Core\Support\Env\Env;
use Exception;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testEnv()
    {
        Env::load(__DIR__ . '/../../.env-example');
        $this->assertEquals('MyFramework', Env::get('APP_NAME'));
    }
}