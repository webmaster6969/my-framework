<?php

declare(strict_types=1);

namespace Core\Routing;

class Redirect
{
    /**
     * @var string
     */
    protected string $url;

    /**
     * @var int
     */
    protected int $status = 302;

    /** @var array<string, string> */
    protected array $headers = [];

    /**
     * @param string $url
     * @param int $status
     * @return self
     */
    public static function to(string $url, int $status = 302): self
    {
        $instance = new self();
        $instance->url = $url;
        $instance->status = $status;
        return $instance;
    }

    /**
     * @param int $status
     * @return self
     */
    public static function back(int $status = 302): self
    {
        $url = is_string($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        return self::to($url, $status);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Redirect
     */
    public function with(string $key, mixed $value): self
    {
        if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        $_SESSION['_flash'][$key] = $value;
        return $this;
    }

    /**
     * @param array<string, string[]> $errors
     * @return Redirect
     */
    public function withErrors(array $errors): self
    {
        $_SESSION['_errors'] = $errors;
        return $this;
    }

    /**
     * @param array<string, string> $headers
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return void
     */
    public function send(): void
    {
        foreach ($this->headers as $key => $value) {
            header("$key: $value", true, $this->status);
        }

        header("Location: {$this->url}", true, $this->status);
        exit;
    }
}