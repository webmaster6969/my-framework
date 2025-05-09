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

    public function match(string $requestUri): array|false
    {
        $pattern = preg_replace('#\{([\w]+)\}#', '(?P<$1>[^/]+)', $this->uri);
        $pattern = "#^{$pattern}$#";

        if (preg_match($pattern, $requestUri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }
}