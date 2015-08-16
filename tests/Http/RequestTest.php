<?php

use Fyuze\Http\Request;

class HttpRequestTest extends PHPUnit_Framework_TestCase
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

        $this->assertEquals('/index.php', $request->getUri());
        $this->assertEquals('/', $request->getPath());
    }

    public function testResolvesFromGlobalsWithIndex()
    {
        $_SERVER['REQUEST_URI'] = '/index.php/foo?bar=baz';
        $request = Request::create();

        $this->assertEquals('/index.php/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPath());
    }

    public function testResolvesDefinedUrl()
    {
        $request = Request::create('/');
        //$this->assertEquals('/', $request->getUri());
        $this->assertEquals('/', $request->getPath());
    }

    public function testGetsHttpHeaders()
    {
        $request = Request::create();
        $this->assertEquals(2, count($request->getHeaders()));
        $this->assertEquals('localhost', $request->header('host'));

        $request->header('host', 'fyuze');
        $this->assertEquals('fyuze', $request->header('host'));

    }

    public function testResolvesUserInput()
    {
        $_POST['foo'] = 'bar';
        $request = Request::create();

        $this->assertArrayHasKey('foo', $request->input());
        $this->assertNull($request->input('bar'));
        $this->assertEquals('bar', $request->input('foo'));

        $request->input('bar', '');
        $this->assertArrayHasKey('bar', $request->input());
        $request->input('bar', 'baz');
        $this->assertEquals('baz', $request->input('bar'));
    }

    public function testResolvesQueryString()
    {
        $request = Request::create('/foo?bar=baz');
        $this->assertEquals('/foo', $request->getPath());
        $this->assertEquals('/foo?bar=baz', $request->getUri());
    }

    public function testResolveIp()
    {
        $request = Request::create();
        $this->assertEquals('127.0.0.1', $request->ip());

        $request->server('REMOTE_ADDR', '127.0.0.2');
        $this->assertEquals('127.0.0.2', $request->ip());

        $request->header('HTTP_X_FORWARDED_FOR', '127.0.0.2,127.0.0.3');
        $this->assertEquals('127.0.0.3', $request->ip());
    }

    public function testDetectsXmlHttpRequest()
    {
        $request = Request::create();
        $request->server('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->assertTrue($request->isAjax());
    }
}
