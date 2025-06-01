<?php

declare(strict_types=1);

namespace Core\Routing;

class Route
{
    public readonly string $method;
    public readonly string $uri;
    public readonly array $action;
    public array $middleware {
        get {
            return $this->middleware;
        }
    }

    public function __construct(string $method, string $uri, array $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->middleware = [];
    }

    public function middleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function match(string $requestUri): array|false
    {
        $pattern = preg_replace('#\{([\w]+)\}#', '(?P<$1>[^/]+)', $this->uri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }


}