<?php

use Fyuze\Http\Request;

class HttpRequestTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        unset($_SERVER['REQUEST_URI']);
        parent::tearDown();
    }

    public function testResolvesFromGlobalsx()
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
