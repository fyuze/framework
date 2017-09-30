<?php

use Fyuze\Http\Message\Request;
use Fyuze\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

class HttpMessageRequestTest extends TestCase
{
    /**
     * @var Request
     */
    protected $request;

    public function setUp()
    {
        $this->request = new Request;
    }

    public function defaultMethodsAndValues()
    {
        return [
            ['getRequestTarget', '/'],
            ['getMethod', null],
            ['getUri', null]
        ];
    }

    /**
     * @dataProvider defaultMethodsAndValues
     */
    public function testGettersReturnExpectedDefaultValues($method, $expected)
    {
        $this->assertEquals($expected, $this->request->{$method}());
    }

    public function testRequestTargetUsesSuppliedValue()
    {
        $request = $this->request->withRequestTarget('/foo?bar=baz');
        $this->assertEquals('/foo?bar=baz', $request->getRequestTarget());
    }

    public function testRootPathIsUsedIfNoTargetOrUriIsSpecified()
    {
        $this->assertEquals('/', $this->request->getRequestTarget());
    }

    public function testRequestTargetIncludesUriQueryString()
    {
        $request = (new Request())->withUri(new Uri('https://localhost/foo?bar=baz'));
        $this->assertEquals('/foo?bar=baz', $request->getRequestTarget());
    }

    public function requestMethods()
    {
        return [
            ['GET'],
            ['HEAD'],
            ['POST'],
            ['PUT'],
            ['OPTIONS'],
            ['DELETE'],
        ];
    }

    /**
     * @dataProvider requestMethods
     */
    public function testAllValidRequestMethodsCanBeUsed($method)
    {
        $request = $this->request->withMethod($method);
        $this->assertEquals($method, $request->getMethod());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRequestMethodThrowsException()
    {
        $this->request->withMethod('FOO');
    }

    public function testHostHeaderIsNotSetIfNotSpecifiedOnUri() {
        $request = $this->request->withUri(
            new Uri('/foo')
        );

        $this->assertEmpty($request->getHeader('host'));
    }

    public function testUriHostIsNotPreservedIfNotSpecified()
    {
        $request = $this->request->withUri(
            new Uri('http://localhost/foo')
        );

        $this->assertEquals('localhost', $request->getHeader('host'));

        $request = $request->withUri(
            new Uri('http://local.dev')
        );

        $this->assertEquals('local.dev', $request->getHeader('host'));
    }

    public function testHostIsPreservedIfSpecified() {
        $request = $this->request->withUri(
            new Uri('http://localhost/foo')
        );

        $this->assertEquals('localhost', $request->getHeader('host'));

        $request = $request->withUri(
            new Uri('http://local.dev'),
            true
        );

        $this->assertEquals('localhost', $request->getHeader('host'));
    }
}
