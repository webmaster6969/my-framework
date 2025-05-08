<?php

namespace Core\Routing;

class Route
{
    public string $method;
    public string $uri;
    public $action;
    public array $middleware = [];

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
    }

    public function middleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }
}
