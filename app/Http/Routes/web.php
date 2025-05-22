<?php

use App\domain\Auth\Presentation\HTTP\AuthController;
use App\domain\Auth\Presentation\HTTP\TotpController;
use App\domain\Auth\Presentation\Middleware\AuthMiddleware;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StorageController;
use App\Http\Middleware\GuestMiddleware;
use Core\Http\Request;
use Core\Routing\Router;

$router = new Router();

$router->group([
    'prefix' => '/',
    'middleware' => [AuthMiddleware::class],
], function (Router $router) {
    $router->get('/users/{id}', [HomeController::class, 'index']);
    $router->get('/storage', [StorageController::class, 'index']);
    $router->post('/storage', [StorageController::class, 'uplode']);
    $router->get('/hello', [HomeController::class, 'hello']);
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/logout', [AuthController::class, 'logout']);
    $router->get('/totp', [TotpController::class, 'index']);
});

$router->group([
    'prefix' => '/',
    'middleware' => [GuestMiddleware::class],
], function (Router $router) {
    $router->get('/login', [AuthController::class, 'index']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'registerForm']);
    $router->post('/register', [AuthController::class, 'register']);
});


$router->dispatch(new Request());
