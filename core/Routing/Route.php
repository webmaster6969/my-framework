<?php

declare(strict_types=1);

namespace Core\Routing;

class Route
{
    /**
     * @var string
     */
    public readonly string $method;

    /**
     * @var string
     */
    public readonly string $uri;

    /** @var array{0: class-string, 1: string} */
    public readonly array $action;

    /** @var array<int, string> */
    public array $middleware;

    /**
     * @param array{0: class-string, 1: string} $action
     */
    public function __construct(string $method, string $uri, array $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->middleware = [];
    }

    /**
     * @param array<int, string> $middleware
     * @return $this
     */
    public function middleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * @param string $requestUri
     * @return array<string, string>|false
     */
    public function match(string $requestUri): array|false
    {
        $requestUri = parse_url($requestUri, PHP_URL_PATH);

        if ($requestUri === false || $requestUri === null) {
            $requestUri = '/';
        }

        $requestUri = rtrim($requestUri, '/') ?: '/';
        $routePath = rtrim($this->uri, '/') ?: '/';

        $pattern = preg_replace('#\{([\w]+)}#', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '/?$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }
}