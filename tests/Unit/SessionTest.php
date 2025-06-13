<?php

declare(strict_types=1);

namespace Unit;

use Core\Support\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @return void
     */
    public function testSession()
    {
        Session::start();
        Session::set('test', 'test');

        $this->assertEquals('test', Session::get('test'));
    }

    /**
     * @return void
     */
    public function testSessionDestroy()
    {
        Session::start();
        Session::forget('test');

        $this->assertFalse(Session::has('test'));
    }
}