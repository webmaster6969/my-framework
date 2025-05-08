<?php

namespace Core\Http\Middleware;

interface MiddlewareInterface
{
    public function handle(callable $next);
}
