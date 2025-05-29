<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\Middleware;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use Core\Http\Middleware\MiddlewareInterface;
use Core\Support\Session\Session;

class TwoFactoryMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): mixed
    {
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        if (!empty($user->getGoogle2faSecret())) {
            if (Session::get('two_factor_auth') !== true) {
                header('Location: /two-factory-auth');
                exit();
            }
        }

        return $next();
    }
}
