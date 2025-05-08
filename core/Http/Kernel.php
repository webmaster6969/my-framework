<?php

namespace Core\Http;

class Kernel
{
    protected array $middleware = [];

    public function __construct(array $middleware = [])
    {
        $this->middleware = $middleware;
    }

    public function handle(callable $controller)
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn($next, $middlewareClass) => fn() => new $middlewareClass()->handle($next),
            $controller
        );

        return $pipeline();
    }
}
