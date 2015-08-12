<?php

use Fyuze\Http\Request;

class HttpRequestTest extends PHPUnit_Framework_TestCase
{
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
