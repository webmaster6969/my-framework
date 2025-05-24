<?php

use App\domain\Auth\Presentation\HTTP\AuthController;
use App\domain\Auth\Presentation\HTTP\TotpController;
use App\domain\Auth\Presentation\Middleware\AuthMiddleware;
use App\domain\Auth\Presentation\Middleware\TwoFactoryMiddleware;
use App\domain\Task\Presentation\HTTP\TaskController;
use App\Http\Controllers\StorageController;
use App\Http\Middleware\GuestMiddleware;
use Core\Http\Request;
use Core\Routing\Router;

$router = new Router();

$router->group([
    'prefix' => '/',
    'middleware' => [AuthMiddleware::class, TwoFactoryMiddleware::class],
], function (Router $router) {
    $router->get('/storage', [StorageController::class, 'index']);
    $router->post('/storage', [StorageController::class, 'uplode']);
    $router->get('/profile', [AuthController::class, 'profile']);
    $router->get('/', [TaskController::class, 'index']);
    $router->get('/logout', [AuthController::class, 'logout']);
    $router->get('/two-factory', [TotpController::class, 'index']);
    $router->post('/two-factory-enable', [TotpController::class, 'enableTwoFactor']);
    $router->post('/two-factory-enable-new', [TotpController::class, 'newAndEnableTwoFactor']);
    $router->post('/two-factory-disable', [TotpController::class, 'disableTwoFactor']);
});

$router->group([
    'prefix' => '/',
    'middleware' => [AuthMiddleware::class],
], function (Router $router) {
    $router->get('/two-factory-auth', [TotpController::class, 'twoFactoryAuth']);
    $router->post('/two-factory-auth-check', [TotpController::class, 'twoFactoryAuthCheck']);
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
