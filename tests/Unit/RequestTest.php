<?php

declare(strict_types=1);

namespace Unit;

use PHPUnit\Framework\TestCase;
use Core\Http\Request;
use RuntimeException;

final class RequestTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $_GET = ['foo' => 'bar'];
        $_POST = ['baz' => 'qux'];
        $_SERVER = ['REQUEST_METHOD' => 'post', 'REQUEST_URI' => '/test/path?query=1'];
        $_COOKIE = ['session' => 'abc123'];
        $_FILES = ['upload' => ['name' => 'file.txt']];
        // set test body
        file_put_contents('php://memory', 'test body');

        // manually instantiate the request to populate static::$request
        new Request();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        // clear static instance for test isolation
        $ref = new \ReflectionClass(Request::class);
        $prop = $ref->getProperty('request');
        $prop->setAccessible(true);
        $prop->setValue(null);
    }

    /**
     * @return void
     */
    public function testMethod(): void
    {
        $this->assertEquals('POST', Request::method());
    }

    /**
     * @return void
     */
    public function testPath(): void
    {
        $this->assertEquals('/test/path', Request::path());
    }

    /**
     * @return void
     */
    public function testFile(): void
    {
        $this->assertEquals(['name' => 'file.txt'], Request::file('upload'));
        $this->assertNull(Request::file('nonexistent'));
    }

    /**
     * @return void
     */
    public function testInput(): void
    {
        $this->assertEquals('qux', Request::input('baz')); // POST
        $this->assertEquals('bar', Request::input('foo')); // fallback to GET
        $this->assertEquals('default', Request::input('missing', 'default'));
    }

    /**
     * @return void
     */
    public function testAll(): void
    {
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], Request::all());
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testHeader(): void
    {
        // simulate header (not always available in CLI)
        $ref = new \ReflectionClass(Request::class);
        $headersProp = $ref->getProperty('headers');
        $headersProp->setAccessible(true);

        $instance = $ref->getMethod('instance')->invoke(null);
        $this->assertInstanceOf(Request::class, $instance);

        $headersProp->setValue($instance, ['X-Test' => 'yes']);

        $this->assertEquals('yes', Request::header('X-Test'));
        $this->assertNull(Request::header('X-Missing'));
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testBody(): void
    {
        // мы не можем переписать php://input напрямую, поэтому используем рефлексию
        $ref = new \ReflectionClass(Request::class);
        $bodyProp = $ref->getProperty('body');
        $bodyProp->setAccessible(true);

        $instance = $ref->getMethod('instance')->invoke(null);
        $this->assertInstanceOf(Request::class, $instance);

        $bodyProp->setValue($instance, 'fake body');

        $this->assertEquals('fake body', Request::body());
    }

    /**
     * @return void
     */
    public function testOnly(): void
    {
        $expected = ['baz' => 'qux'];
        $this->assertEquals($expected, Request::only(['baz']));
    }

    /**
     * @return void
     */
    public function testInstanceNotInitialized(): void
    {
        // Reset instance
        $ref = new \ReflectionClass(Request::class);
        $prop = $ref->getProperty('request');
        $prop->setAccessible(true);
        $prop->setValue(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Request is not initialized.');
        Request::method();
    }
}