<?php

declare(strict_types=1);

namespace Core\Routing;

use Closure;
use Core\Http\Kernel;
use Core\Http\Request;
use Core\Http\Middleware\MiddlewareInterface;
use Core\Response\Response;
use Exception;

class Router
{
    /** @var array<int, Route> */
    protected array $routes = [];

    /** @var array{prefix?: string, middleware?: array<int, class-string<MiddlewareInterface>>} */
    protected array $groupAttributes = [];

    /**
     * @param string $uri
     * @param array{0: class-string, 1: string} $action
     * @return Route
     */
    public function get(string $uri, array $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * @param string $uri
     * @param array{0: class-string, 1: string} $action
     * @return Route
     */
    public function post(string $uri, array $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * @param string $uri
     * @param array{0: class-string, 1: string} $action
     * @return Route
     */
    public function put(string $uri, array $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * @param string $uri
     * @param array{0: class-string, 1: string} $action
     * @return Route
     */
    public function delete(string $uri, array $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array{0: class-string, 1: string} $action
     * @return Route
     */
    public function addRoute(string $method, string $uri, array $action): Route
    {
        $prefix = $this->groupAttributes['prefix'] ?? '';
        $uri = rtrim($prefix, '/') . '/' . ltrim($uri, '/');
        $route = new Route($method, $uri, $action);

        if (!empty($this->groupAttributes['middleware'])) {
            /** @var array<int, class-string<MiddlewareInterface>> $middleware */
            $middleware = $this->groupAttributes['middleware'];
            $route->middleware($middleware);
        }

        $this->routes[] = $route;
        return $route;
    }

    /**
     * @param array{prefix?: string, middleware?: array<int, class-string<MiddlewareInterface>>} $attributes
     * @param Closure $callback
     */
    public function group(array $attributes, Closure $callback): void
    {
        $previousGroup = $this->groupAttributes;

        if (!empty($previousGroup)) {
            if (isset($previousGroup['prefix']) && isset($attributes['prefix'])) {
                $previousPrefix = $previousGroup['prefix'];
                $attributesPrefix = $attributes['prefix'];
                $attributes['prefix'] = rtrim($previousPrefix, '/') . '/' . ltrim($attributesPrefix, '/');
            } elseif (isset($previousGroup['prefix'])) {
                $attributes['prefix'] = $previousGroup['prefix'];
            }

            if (isset($previousGroup['middleware']) && isset($attributes['middleware'])) {
                /** @var array<int, class-string<MiddlewareInterface>> $mergedMiddleware */
                $mergedMiddleware = array_merge(
                    (array)$previousGroup['middleware'],
                    (array)$attributes['middleware']
                );
                $attributes['middleware'] = $mergedMiddleware;
            } elseif (isset($previousGroup['middleware'])) {
                $attributes['middleware'] = $previousGroup['middleware'];
            }
        }

        $this->groupAttributes = $attributes;
        $callback($this);
        $this->groupAttributes = $previousGroup;
    }

    /**
     * @throws Exception
     */
    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($route->method === $request->method() && $route->uri === $request->path()) {
                $params = $route->match($request->path());

                if ($params !== false) {
                    $handler = function () use ($route, $params): mixed {
                        [$class, $method] = $route->action;
                        return new $class()->$method(...array_values($params));
                    };

                    $middlewares = array_map(function (string $middlewareClass): MiddlewareInterface {
                        $middleware = new $middlewareClass();
                        if (!$middleware instanceof MiddlewareInterface) {
                            throw new \RuntimeException("Middleware must implement MiddlewareInterface");
                        }
                        return $middleware;
                    }, $route->middleware);


                    $kernel = new Kernel($middlewares);
                    $response = $kernel->handle($handler);

                    if ($response instanceof Response) {
                        $response->send();
                    } else {
                        http_response_code(500);
                        echo "Invalid response from handler.";
                    }

                    return;
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}