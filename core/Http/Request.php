<?php

declare(strict_types=1);

namespace Core\Http;

class Request
{
    /** @var array<string, mixed> */
    protected array $get;

    /** @var array<string, mixed> */
    protected array $post;

    /** @var array<string, mixed> */
    protected array $server;

    /** @var array<string, mixed> */
    protected array $cookies;

    /** @var array<string, mixed> */
    protected array $files;
    /** @var array<string, string> */
    protected array $headers;

    /** @var string|false */
    protected string|false $body;

    /** @var Request|null */
    protected static ?Request $request = null;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->headers = function_exists('getallheaders') ? getallheaders() : [];
        $this->body = file_get_contents('php://input');

        static::$request = $this;
    }

    /**
     * @return Request
     */
    protected static function instance(): Request
    {
        if (!static::$request) {
            throw new \RuntimeException('Request is not initialized.');
        }
        return static::$request;
    }

    /**
     * @return string
     */
    public static function method(): string
    {
        $method = static::instance()->server['REQUEST_METHOD'] ?? null;
        return strtoupper(is_string($method) ? $method : 'GET');
    }

    /**
     * @return string
     */
    public static function path(): string
    {
        $uri = static::instance()->server['REQUEST_URI'] ?? null;
        $uri = is_string($uri) ? $uri : '/';
        return strtok($uri, '?') ?: '/';
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function file(string $key): mixed
    {
        return static::instance()->files[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function input(string $key, mixed $default = null): mixed
    {
        return static::instance()->post[$key] ?? static::instance()->get[$key] ?? $default;
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        return array_merge(static::instance()->get, static::instance()->post);
    }

    /**
     * @param string $key
     * @return string|null
     */
    public static function header(string $key): ?string
    {
        return static::instance()->headers[$key] ?? null;
    }

    /**
     * @return string|false
     */
    public static function body(): string|false
    {
        return static::instance()->body;
    }
}
