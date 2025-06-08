<?php

namespace Unit;

use App\domain\Auth\Presentation\HTTP\AuthController;
use Core\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @return void
     */
    public function testRouteMatching()
    {
        $router = new Route('GET', '/home', [AuthController::class, 'index']);


        $this->assertEquals([AuthController::class, 'index'], $router->action);
        $this->assertEquals('/home', $router->uri);
        $this->assertEquals('GET', $router->method);
    }
}