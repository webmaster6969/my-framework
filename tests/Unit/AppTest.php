<?php

declare(strict_types=1);

namespace Unit;

use Core\Support\App\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{

    /**
     * @return void
     */
    public function testInitAndGetBasePath(): void
    {
        $basePath = '/var/www/myapp';

        App::init($basePath);

        $this->assertSame($basePath, App::getBasePath());
    }
}