<?php

namespace App\Http\Middleware;

use Core\Http\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        if (!isset($_GET['token']) || $_GET['token'] !== 'secret') {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        return $next();
    }
}
