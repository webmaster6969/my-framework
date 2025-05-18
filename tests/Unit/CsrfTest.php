<?php

namespace Tests\Unit;

use Core\Support\Csrf\Csrf;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class CsrfTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testCsrf()
    {
        $token = Csrf::token();
        $this->assertTrue(Csrf::check($token));
    }
}