<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\GuestMiddleware;
use Core\Http\Request;
use Core\Routing\Router;

$router = new Router();

$router->group([
    'prefix' => '/',
    'middleware' => [AuthMiddleware::class],
], function ($router) {
    $router->get('/users/{id}', [HomeController::class, 'index']);
    $router->get('/hello', [HomeController::class, 'hello']);
    $router->get('/', [HomeController::class, 'index']);
});

$router->group([
    'prefix' => '/',
    'middleware' => [GuestMiddleware::class],
], function ($router) {
    $router->get('/login', [AuthController::class, 'index']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'registerForm']);
    $router->post('/register', [AuthController::class, 'register']);
});


$router->dispatch(new Request());
