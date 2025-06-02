<?php

namespace Core\Response;

use Core\Routing\Redirect;
use Core\View\View;
use Exception;

class Response
{
    private array $headers = [];
    private int $status = 200;
    private mixed $content;
    public static function make(mixed $data, int $status = 200, array $headers = []): Response
    {
        $response = new Response();
        $response->setContent($data);
        $response->withStatus($status);
        $response->withHeaders($headers);
        return $response;
    }

    public function withHeaders(array $headers): Response
    {
        $this->headers = $headers;
        return $this;
    }

    public function withStatus(int $status): Response
    {
        $this->status = $status;
        return $this;
    }

    public function setContent(mixed $content): Response
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function send(): void
    {
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        if ($this->content instanceof View) {
            http_response_code($this->status);
            echo $this->content->render();
        }

        if ($this->content instanceof Redirect) {
            $this->content->send();
        }
    }
}