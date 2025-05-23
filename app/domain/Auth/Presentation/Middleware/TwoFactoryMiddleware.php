<?php

namespace App\domain\Auth\Presentation\Middleware;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Service\AuthService;
use Core\Http\Middleware\MiddlewareInterface;
use Core\Support\Session\Session;

class TwoFactoryMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): mixed
    {
        $authService = new AuthService(new UserRepositories());
        $user = $authService->getUser();

        if (!empty($user->getGoogle2faSecret())) {
            if (Session::get('two_factor_auth') !== true) {
                header('Location: /two-factory-auth');
                exit();
            }
        }

        return $next();
    }
}
