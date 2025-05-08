<?php

use App\Http\Controllers\HomeController;
use App\Http\Middleware\AuthMiddleware;
use Core\Routing\Router;

$router = new Router();

$router->get('/admin', [HomeController::class, 'index'])
    ->middleware([AuthMiddleware::class]);

$router->get('/', [HomeController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
