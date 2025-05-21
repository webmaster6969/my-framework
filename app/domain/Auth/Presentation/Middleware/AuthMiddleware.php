<?php

namespace App\domain\Auth\Presentation\Middleware;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Service\AuthService;
use Core\Http\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): mixed
    {
        $authService = new AuthService(new UserRepositories());
        $user = $authService->getUser();

        if (empty($user)) {
            header('Location: /login');
            exit();
        }

        return $next();
    }
}
