<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\AuthMiddleware;
use Core\Http\Request;
use Core\Routing\Router;

$router = new Router();

$router->group([
    'prefix' => '/admin',
    'middleware' => [AuthMiddleware::class],
], function ($router) {
    $router->get('/users/{id}', [HomeController::class, 'index']);
});

$router->get('/admin', [HomeController::class, 'index'])
    ->middleware([AuthMiddleware::class]);

$router->get('/', [HomeController::class, 'index']);

$router->get('/users/{id}', [ApiController::class, 'index']);

$router->get('/login', [AuthController::class, 'index']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/hello', [AuthController::class, 'hello']);


$router->dispatch(new Request());
