<?php

use Fyuze\Http\Message\ServerRequest;

class HttpMessageServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServerRequest
     */
    protected $server;

    public function setUp()
    {
        $this->server = new ServerRequest();
    }

    public function getterProvider()
    {
        return [
            ['getServerParams'], ['getCookieParams'], ['getQueryParams'],
            ['getUploadedFiles'], ['getAttributes'], ['getParsedBody']
        ];
    }

    public function mutableProvider()
    {
        return [
            ['withCookieParams'], ['withQueryParams'],
            ['withUploadedFiles'], ['withParsedBody']
        ];
    }

    /**
     * @dataProvider getterProvider
     */
    public function testDefaultGetterValues($method)
    {
        $this->assertEmpty($this->server->{$method}());
    }

    /**
     * @dataProvider mutableProvider
     */
    public function testWithMethodsAreImmutable($method)
    {
        $server = $this->server->{$method}(['foo' => 'bar']);
        $this->assertNotSame($this->server, $server);
    }

    public function testAttributesAreImmutableAndRemoveable()
    {
        $this->assertNull($this->server->getAttribute('foo'));
        $server = $this->server->withAttribute('foo', 'bar');

        $this->assertNotSame($this->server, $server);
        $this->assertEquals('bar', $server->getAttribute('foo'));

        $server2 = $server->withoutAttribute('foo');
        $this->assertNotSame($server, $server2);
        $this->assertNotSame($server2, $server2->withoutAttribute('foo'));
        $this->assertNull($server2->getAttribute('foo'));
    }
}
