<?php

use Fyuze\Http\Response;

class HttpResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Fyuze\Http\Response
     */
    protected $response;

    public function setUp()
    {
        $this->response = Response::create();
    }

    public function testBasicResponse()
    {
        $this->response->getBody()->write('Hello, World!');

        $this->assertEquals('Hello, World!', $this->response->getBody());
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testResponseHeaders()
    {
        $response = (new \Fyuze\Http\Response())
            ->withHeader('key', 'value');

        $this->assertEquals('value', $response->getHeader('key')[0]);
    }

    public function testCacheToggle()
    {
        // default value is false
        $this->assertFalse($this->response->setCache());
        $this->assertTrue($this->response->setCache(true));
    }


    public function testCompressionToggle()
    {
        // default value is true
        $this->assertTrue($this->response->setCompression());
        $this->assertFalse($this->response->setCompression(false));
    }

    public function testStaticCreationMethod()
    {
        $response = \Fyuze\Http\Response::create('foobar');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foobar', (string)$response->getBody());
    }

    public function testModifyingTheResponseBody()
    {
        $this->response->getBody()->write('hello');
        $this->response->modify(function ($body) {
            return str_replace('hello', 'hola', $body);
        });

        $this->assertEquals('hola', (string)$this->response->getBody());
    }

    public function testResponseToString()
    {
        $this->response->getBody()->write('foo');
        $this->assertEquals('foo', (string)$this->response);
    }
}
