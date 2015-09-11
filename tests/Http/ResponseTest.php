<?php

class HttpResponseTest extends PHPUnit_Framework_TestCase
{
    public function testBasicResponse()
    {
        $response = new \Fyuze\Http\Response('Hello, World!');
        $response->setCache();
        $response->setCompression();

        $this->assertEquals('Hello, World!', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResponseHeaders()
    {
        $response = new \Fyuze\Http\Response();
        $response->header('key', 'value');

        $this->assertEquals($response->header('key'), 'value');
    }

    public function testModifyResponse() {
        $response = new \Fyuze\Http\Response('hello');
        $response->modify(function($body) {
           return str_replace('hello', 'hola', $body);
        });

        $this->assertEquals('hola', $response->getBody());
    }
}
