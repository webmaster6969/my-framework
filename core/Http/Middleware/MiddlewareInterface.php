<?php

declare(strict_types=1);

namespace Core\Http\Middleware;

interface MiddlewareInterface
{
    public function handle(callable $next);
}
