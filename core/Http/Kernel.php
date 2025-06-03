<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Http\Middleware\MiddlewareInterface;

class Kernel
{
    /** @var array<MiddlewareInterface> */
    protected array $middleware = [];

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function __construct(array $middleware = [])
    {
        $this->middleware = $middleware;
    }

    /**
     * @param callable $controller
     * @return mixed
     */
    public function handle(callable $controller): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn($next, $middlewareClass) => fn() => new $middlewareClass()->handle($next),
            $controller
        );

        return $pipeline();
    }
}
