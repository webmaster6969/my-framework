<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\AuthMiddleware;
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


$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
