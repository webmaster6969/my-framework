<?php

namespace App\Http\Middleware;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Service\AuthService;
use Core\Http\Middleware\MiddlewareInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): mixed
    {
        $authService = new AuthService(new UserRepositories());
        $user = $authService->getUser();

        if (!empty($user)) {
            header('Location: /profile');
            exit();
        }

        return $next();
    }
}
