<?php

namespace Core\Http;

class Request
{
    protected array $get;
    protected array $post;
    protected array $server;
    protected array $cookies;
    protected array $files;
    protected array $headers;
    protected string $body;
    protected static ?Request $request = null;

    public static function object(): Request
    {
        if (static::$request !== null) {
            return static::$request;
        }
        static::$request = new Request();
        return static::$request;
    }

    public function __construct()
    {
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->server  = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->files   = $_FILES;
        $this->headers = function_exists('getallheaders') ? getallheaders() : [];
        $this->body    = file_get_contents('php://input');
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return strtok($uri, '?');
    }

    public function input(string $key, $default = null): mixed
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function header(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    public function body(): string
    {
        return $this->body;
    }
}
