<?php

use Fyuze\Http\Message\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HttpMessageServerTest extends TestCase
{
    /**
     * @var ServerRequest
     */
    protected $server;

    public function setUp(): void
    {
        $this->server = new ServerRequest();
    }

    public static function getterProvider()
    {
        return [
            ['getServerParams'], ['getCookieParams'], ['getQueryParams'],
            ['getUploadedFiles'], ['getAttributes'], ['getParsedBody']
        ];
    }

    public static function mutableProvider()
    {
        return [
            ['withCookieParams'], ['withQueryParams'],
            ['withUploadedFiles'], ['withParsedBody']
        ];
    }

    #[DataProvider('getterProvider')]
    public function testDefaultGetterValues($method)
    {
        $this->assertEmpty($this->server->{$method}());
    }

    #[DataProvider('mutableProvider')]
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
