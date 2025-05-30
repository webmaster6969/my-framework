<?php

namespace Core\Routing;

class Redirect
{
    protected string $url;
    protected int $status = 302;
    protected array $headers = [];

    public static function to(string $url, int $status = 302): self
    {
        $instance = new self();
        $instance->url = $url;
        $instance->status = $status;
        return $instance;
    }

    public static function back(int $status = 302): self
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';
        return self::to($url, $status);
    }

    public function with(string $key, $value): self
    {
        $_SESSION['_flash'][$key] = $value;
        return $this;
    }

    public function withErrors(array $errors): self
    {
        $_SESSION['_errors'] = $errors;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function send(): void
    {
        foreach ($this->headers as $key => $value) {
            header("$key: $value", true, $this->status);
        }

        header("Location: {$this->url}", true, $this->status);
        exit;
    }
}