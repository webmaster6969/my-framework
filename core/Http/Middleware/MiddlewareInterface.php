<?php

declare(strict_types=1);

namespace Core\Http\Middleware;

interface MiddlewareInterface
{
    /**
     * @param callable $next
     * @return mixed
     */
    public function handle(callable $next): mixed;
}
