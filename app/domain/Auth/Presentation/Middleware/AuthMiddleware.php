<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\Middleware;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use Core\Http\Middleware\MiddlewareInterface;
use Core\Routing\Redirect;
use Core\Support\Session\Session;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @param callable $next
     * @return mixed
     */
    public function handle(callable $next): mixed
    {
        $userId = Session::get('user_id');
        if (empty($userId) || !is_int($userId)) {
            return $next();
        }

        $findUserQuery = new FindUserQuery(new UserRepositories(), $userId);
        $user = $findUserQuery->handle();

        if (empty($user)) {
            Redirect::to('/login')->send();
        }

        return $next();
    }
}
