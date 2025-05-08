<?php

namespace Core\Routing;

class Route
{
    public string $uri;
    public string $controller;
    public string $method;

    public function __construct(string $uri, string $controller, string $method = 'index')
    {
        $this->uri = $uri;
        $this->controller = $controller;
        $this->method = $method;
    }
}
