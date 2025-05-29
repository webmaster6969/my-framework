<?php

declare(strict_types=1);

use App\domain\Auth\Presentation\HTTP\AuthController;
use App\domain\Auth\Presentation\HTTP\ProfileController;
use App\domain\Auth\Presentation\HTTP\TotpController;
use App\domain\Auth\Presentation\Middleware\AuthMiddleware;
use App\domain\Auth\Presentation\Middleware\TwoFactoryMiddleware;
use App\domain\Storage\Presentation\HTTP\StorageController;
use App\domain\Task\Presentation\HTTP\TaskController;
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
    $router->get('/profile', [ProfileController::class, 'index']);
    $router->post('/profile/update', [ProfileController::class, 'update']);

    $router->get('/', [TaskController::class, 'index']);
    $router->get('/tasks', [TaskController::class, 'index']);
    $router->get('/tasks/create', [TaskController::class, 'create']);
    $router->post('/tasks/store', [TaskController::class, 'store']);

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
