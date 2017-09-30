<?php

use Fyuze\Http\Request;
use PHPUnit\Framework\TestCase;

class HttpRequestTest extends TestCase
{
    public function tearDown()
    {
        unset($_SERVER['REQUEST_URI']);
        parent::tearDown();
    }

    public function testResolvesFromGlobals()
    {
        $_SERVER['REQUEST_URI'] = '/index.php';
        $request = Request::create();

        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function testResolvesFromGlobalsWithIndex()
    {
        $_SERVER['REQUEST_URI'] = '/index.php/foo?bar=baz';
        $request = Request::create('/index.php/foo?bar=baz');

        $this->assertEquals('/foo?bar=baz', (string) $request->getUri());
        $this->assertEquals('/foo', $request->getUri()->getPath());
    }

    public function testResolvesDefinedUrl()
    {
        $request = Request::create('/');
        $this->assertEquals('/', $request->getUri());
    }

    public function testGetsHttpHeaders()
    {
        $request = Request::create()->withHeader('host', 'fyuze');
        $this->assertEquals(1, count($request->getHeaders()));

        $this->assertEquals('fyuze', $request->getHeaderLine('host'));

    }

    public function testResolvesUserInput()
    {
        $_POST['foo'] = 'bar';
        $request = Request::create();

        $this->assertArrayHasKey('foo', $request->getParsedBody());
//        $this->assertNull($request->input('bar'));
//        $this->assertEquals('bar', $request->input('foo'));
//
//        $request->input('bar', '');
//        $this->assertArrayHasKey('bar', $request->input());
//        $request->input('bar', 'baz');
//        $this->assertEquals('baz', $request->input('bar'));
    }

    public function testResolvesQueryString()
    {
        $request = Request::create('/foo?bar=baz');
        $this->assertEquals('/foo', $request->getUri()->getPath());
        $this->assertEquals('/foo?bar=baz', $request->getUri());
    }

    public function testResolveIp()
    {
        $request = Request::create();
        $this->assertEquals('127.0.0.1', $request->getServerParams()['REMOTE_ADDR']);
    }
}
