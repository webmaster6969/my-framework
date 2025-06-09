<?php

declare(strict_types=1);

namespace UnitMock;

use Core\Response\Response;
use Core\Routing\Redirect;
use Core\View\View;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class ResponseTest extends TestCase
{
    /**
     * @return void
     */
    public function testMakeCreatesResponseWithCorrectProperties(): void
    {
        $data = 'Hello, World!';
        $status = 201;
        $headers = ['Content-Type' => 'text/plain'];

        $response = Response::make($data, $status, $headers);

        $this->assertInstanceOf(Response::class, $response);

        $reflection = new ReflectionClass($response);
        $statusProp = $reflection->getProperty('status');
        $statusProp->setAccessible(true);
        $headersProp = $reflection->getProperty('headers');
        $headersProp->setAccessible(true);
        $contentProp = $reflection->getProperty('content');
        $contentProp->setAccessible(true);

        $this->assertSame($status, $statusProp->getValue($response));
        $this->assertSame($headers, $headersProp->getValue($response));
        $this->assertSame($data, $contentProp->getValue($response));
    }

    /**
     * @return void
     */
    public function testSetContentReturnsSelf(): void
    {
        $response = new Response();
        $result = $response->setContent('Test');
        $this->assertSame($response, $result);
    }

    /**
     * @return void
     */
    public function testWithStatusReturnsSelf(): void
    {
        $response = new Response();
        $result = $response->withStatus(404);
        $this->assertSame($response, $result);
    }

    /**
     * @return void
     */
    public function testWithHeadersReturnsSelf(): void
    {
        $response = new Response();
        $result = $response->withHeaders(['X-Test' => 'Value']);
        $this->assertSame($response, $result);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSendWithViewContent(): void
    {
        $view = $this->createMock(View::class);
        $view->expects($this->once())->method('render')->willReturn('Rendered View');

        $response = new Response();
        $response->withStatus(200)->setContent($view);

        ob_start();
        $response->send();
        $output = ob_get_clean();

        $this->assertSame('Rendered View', $output);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSendWithRedirectContent(): void
    {
        $redirect = $this->createMock(Redirect::class);
        $redirect->expects($this->once())->method('send');

        $response = new Response();
        $response->setContent($redirect);

        $response->send();
    }
}