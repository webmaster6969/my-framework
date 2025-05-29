<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Core\Http\Middleware\MiddlewareInterface;

class ExampleMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        // можно вставить логирование, проверку и пр.
        error_log('Middleware: before controller');

        $response = $next();

        error_log('Middleware: after controller');

        return $response;
    }
}
