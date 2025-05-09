<?php

namespace Core\Routing;

use Core\Http\Kernel;

class Router
{
    protected array $routes = [];

    public function get(string $uri, $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function delete(string $uri, $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function patch(string $uri, $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    protected function addRoute(string $method, string $uri, $action): Route
    {
        $route = new Route($method, $uri, $action);
        $this->routes[] = $route;
        return $route;
    }

    public function dispatch(string $method, string $uri)
    {
        foreach ($this->routes as $route) {
            if ($route->method === $method) {
                $params = $route->match($uri);

                if ($params !== false) {
                    $handler = function () use ($route, $params) {
                        [$class, $method] = $route->action;
                        return (new $class)->$method(...array_values($params));
                    };

                    $kernel = new Kernel($route->middleware);
                    return $kernel->handle($handler);
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

}