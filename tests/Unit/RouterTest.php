<?php

namespace Tests\Unit;

use Core\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRouteMatching()
    {
        $router = new Route('GET', '/home', ['HomeController', 'index']);

        $this->assertEquals(['HomeController', 'index'], $router->action);
        $this->assertEquals('/home', $router->uri);
        $this->assertEquals('GET', $router->method);
    }
}