<?php

namespace Core\Routing;

class Router
{
    private array $routes = [];

    public function get(string $uri, string $controller): void
    {
        $this->routes[] = new Route($uri, $controller);
    }

    public function dispatch(string $requestUri): mixed
    {
        $requestUri = parse_url($requestUri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route->uri === $requestUri) {
                [$controllerClass, $method] = explode('@', $route->controller);
                $controller = new $controllerClass();
                return $controller->$method();
            }
        }

        http_response_code(404);
        echo '404 Not Found';
        return null;
    }
}
