<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Http\Kernel;
use Core\Http\Request;

class Router
{
    protected array $routes = [];

    protected array $groupAttributes = [];

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

    public function addRoute(string $method, string $uri, $action): Route
    {
        // Применение префикса из группы, если есть
        if (!empty($this->groupAttributes['prefix'])) {
            $uri = rtrim($this->groupAttributes['prefix'], '/') . '/' . ltrim($uri, '/');
        }

        $route = new Route($method, $uri, $action);

        // Применение middleware из группы, если есть
        if (!empty($this->groupAttributes['middleware'])) {
            $route->middleware($this->groupAttributes['middleware']);
        }

        $this->routes[] = $route;
        return $route;
    }

    public function group(array $attributes, \Closure $callback)
    {
        $previousGroup = $this->groupAttributes;

        // Слияние middleware и префикса
        if (!empty($previousGroup)) {
            if (isset($previousGroup['prefix']) && isset($attributes['prefix'])) {
                $attributes['prefix'] = rtrim($previousGroup['prefix'], '/') . '/' . ltrim($attributes['prefix'], '/');
            } elseif (isset($previousGroup['prefix'])) {
                $attributes['prefix'] = $previousGroup['prefix'];
            }

            if (isset($previousGroup['middleware']) && isset($attributes['middleware'])) {
                $attributes['middleware'] = array_merge($previousGroup['middleware'], $attributes['middleware']);
            } elseif (isset($previousGroup['middleware'])) {
                $attributes['middleware'] = $previousGroup['middleware'];
            }
        }

        $this->groupAttributes = $attributes;

        $callback($this);

        $this->groupAttributes = $previousGroup;
    }

    public function dispatch(Request $request)
    {
        foreach ($this->routes as $route) {
            if ($route->method === $request->method() && $route->uri === $request->path()) {
                $params = $route->match($request->path());

                if ($params !== false) {
                    $handler = function () use ($route, $params) {
                        [$class, $method] = $route->action;
                        return (new $class)->$method(...array_values($params));

                    };
                    $kernel = new Kernel($route->middleware);
                    $response = $kernel->handle($handler);

                    $response->send();

                    return;
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
