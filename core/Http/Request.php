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

    public static function method(): string
    {
        return strtoupper(static::$request->server['REQUEST_METHOD'] ?? 'GET');
    }

    public static function path(): string
    {
        $uri = static::$request->server['REQUEST_URI'] ?? '/';
        return strtok($uri, '?');
    }

    public static function input(string $key, $default = null): mixed
    {
        return static::$request->post[$key] ?? static::$request->get[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge(static::$request->get, static::$request->post);
    }

    public static function header(string $key): ?string
    {
        return static::$request->headers[$key] ?? null;
    }

    public static function body(): string
    {
        return static::$request->body;
    }
}
