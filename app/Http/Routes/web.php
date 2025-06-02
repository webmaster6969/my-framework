<?php

declare(strict_types=1);

use App\domain\Auth\Presentation\HTTP\AuthController;
use App\domain\Auth\Presentation\HTTP\ProfileController;
use App\domain\Auth\Presentation\HTTP\TotpController;
use App\domain\Auth\Presentation\Middleware\AuthMiddleware;
use App\domain\Auth\Presentation\Middleware\GuestMiddleware;
use App\domain\Auth\Presentation\Middleware\TwoFactoryMiddleware;
use App\domain\Common\Presentation\HTTP\CsrfMiddleware;
use App\domain\Storage\Presentation\HTTP\StorageController;
use App\domain\Task\Presentation\HTTP\TaskController;
use Core\Http\Request;
use Core\Routing\Router;

$router = new Router();

$router->group([
    'prefix' => '/',
    'middleware' => [AuthMiddleware::class, TwoFactoryMiddleware::class],
], function (Router $router) {
    $router->get('/storage', [StorageController::class, 'index']);
    $router->post('/storage', [StorageController::class, 'uplode'])->middleware([CsrfMiddleware::class]);;
    $router->get('/profile', [ProfileController::class, 'index']);
    $router->post('/profile/update', [ProfileController::class, 'update'])->middleware([CsrfMiddleware::class]);;

    $router->get('/', [TaskController::class, 'index']);
    $router->get('/tasks', [TaskController::class, 'index']);
    $router->get('/tasks/create', [TaskController::class, 'create']);
    $router->get('/tasks/edit/', [TaskController::class, 'edit']);
    $router->get('/tasks/delete/', [TaskController::class, 'delete']);

    $router->post('/tasks/store', [TaskController::class, 'store'])->middleware([CsrfMiddleware::class]);
    $router->post('/tasks/update/', [TaskController::class, 'update'])->middleware([CsrfMiddleware::class]);

    $router->get('/logout', [AuthController::class, 'logout']);
    $router->get('/two-factory', [TotpController::class, 'index']);
    $router->post('/two-factory-enable', [TotpController::class, 'enableTwoFactor'])->middleware([CsrfMiddleware::class]);;
    $router->post('/two-factory-enable-new', [TotpController::class, 'newAndEnableTwoFactor'])->middleware([CsrfMiddleware::class]);;
    $router->post('/two-factory-disable', [TotpController::class, 'disableTwoFactor'])->middleware([CsrfMiddleware::class]);;
});

$router->group([
    'prefix' => '/',
    'middleware' => [AuthMiddleware::class],
], function (Router $router) {
    $router->get('/two-factory-auth', [TotpController::class, 'twoFactoryAuth']);
    $router->post('/two-factory-auth-check', [TotpController::class, 'twoFactoryAuthCheck'])->middleware([CsrfMiddleware::class]);;
});

$router->group([
    'prefix' => '/',
    'middleware' => [GuestMiddleware::class],
], function (Router $router) {
    $router->get('/login', [AuthController::class, 'index']);
    $router->post('/login', [AuthController::class, 'login'])->middleware([CsrfMiddleware::class]);;
    $router->get('/register', [AuthController::class, 'registerForm']);
    $router->post('/register', [AuthController::class, 'register'])->middleware([CsrfMiddleware::class]);;
});


$router->dispatch(new Request());
