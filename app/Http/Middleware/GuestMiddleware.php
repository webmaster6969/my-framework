<?php

namespace App\Http\Middleware;

use Core\Http\Middleware\MiddlewareInterface;
use Core\Support\Auth;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): mixed
    {
        $user = Auth::user();
        if (!empty($user)) {
            header('Location: /hello');
            exit();
        }

        return $next();
    }
}
