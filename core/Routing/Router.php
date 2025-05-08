<?php

namespace Core\Routing;

use Core\Http\Kernel;

class Router
{
    protected array $routes = [];

    public function get(string $uri, $action): Route
    {
        $route = new Route('GET', $uri, $action);
        $this->routes[] = $route;
        return $route;
    }

    public function dispatch(string $method, string $uri)
    {
        foreach ($this->routes as $route) {
            if ($route->method === $method && $route->uri === $uri) {
                $handler = function () use ($route) {
                    [$class, $method] = $route->action;
                    return (new $class)->$method();
                };

                $kernel = new Kernel($route->middleware);
                return $kernel->handle($handler);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
