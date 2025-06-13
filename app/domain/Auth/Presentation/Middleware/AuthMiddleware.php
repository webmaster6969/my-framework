<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\Middleware;

use App\domain\Auth\Services\AuthService;
use Core\Http\Middleware\MiddlewareInterface;
use Core\Routing\Redirect;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @param callable $next
     * @return mixed
     */
    public function handle(callable $next): mixed
    {
        $user = AuthService::getUser();
        if (!empty($user)) {
            return $next();
        }

        Redirect::to('/login')->send();

        return $next();
    }
}
