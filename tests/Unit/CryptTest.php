<?php

namespace Tests\Unit;

use Core\Support\Crypt\Crypt;
use PHPUnit\Framework\TestCase;

class CryptTest extends TestCase
{
    public function testCrypt()
    {
        new Crypt('test');
        $data = Crypt::encrypt('test');

        $this->assertEquals('test', Crypt::decrypt($data));
    }
}