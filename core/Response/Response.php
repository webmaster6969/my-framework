<?php

namespace Core\Response;

use Core\Routing\Redirect;
use Core\View\View;
use Exception;

class Response
{
    /** @var array<string, string> */
    private array $headers = [];
    /**
     * @var int
     */
    private int $status = 200;
    /**
     * @var mixed
     */
    private mixed $content;

    /**
     * @param array<string, string> $headers
     */
    public static function make(mixed $data, int $status = 200, array $headers = []): Response
    {
        $response = new Response();
        $response->setContent($data);
        $response->withStatus($status);
        $response->withHeaders($headers);
        return $response;
    }

    /**
     * @param array<string, string> $headers
     */
    public function withHeaders(array $headers): Response
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function withStatus(int $status): Response
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param mixed $content
     * @return $this
     */
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