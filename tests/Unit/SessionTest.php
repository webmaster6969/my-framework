<?php

namespace Tests\Unit;

use Core\Support\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function testSession()
    {
        Session::start();
        Session::set('test', 'test');

        $this->assertEquals('test', Session::get('test'));
    }

    public function testSessionDestroy()
    {
        Session::start();
        Session::forget('test');

        $this->assertFalse(Session::has('test'));
    }
}