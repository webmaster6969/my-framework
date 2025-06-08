<?php

namespace Unit;

use Core\Support\Csrf\Csrf;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class CsrfTest extends TestCase
{
    /**
     * @return void
     * @throws RandomException
     */
    public function testCsrf()
    {
        $token = Csrf::token();
        $this->assertTrue(Csrf::check($token));
    }
}