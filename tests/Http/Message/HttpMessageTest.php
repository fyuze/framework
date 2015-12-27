<?php

use Fyuze\Http\Message\Message;

class HttpMessageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Message
     */
    protected $message;

    public function setUp()
    {
        $this->message = new Message();
    }

    public function testProtocolResolvesValidValue()
    {
        $message = $this->message->withProtocolVersion('1.0');

        $this->assertEquals('1.0', $message->getProtocolVersion());

        $message->withProtocolVersion('1.1');

        // Immutability test
        $this->assertEquals('1.0', $message->getProtocolVersion());
        $this->assertEquals('1.1', $message->withProtocolVersion('1.1')->getProtocolVersion());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testProtocolThrowsErrorOnValidValue()
    {
        $this->message->withProtocolVersion('1.3');
    }

    public function testSetsAndGetsHeaders()
    {
        $message = (new \Fyuze\Http\Message\Message())->withHeader('foo', 'bar');

        $this->assertTrue($message->hasHeader('foo'));
        $this->assertTrue($message->hasHeader('fOO'));
        $this->assertEquals(['bar'], $message->getHeader('foo'));
        $this->assertEquals(['bar'], $message->getHeader('Foo'));
        $this->assertEmpty($message->getHeader('bar'));

        // Make sure empty value will still return an array
        $this->assertInternalType('array', $message->withHeader('bar', '')->getHeader('bar'));
        $this->assertEmpty($message->withHeader('bar', '')->getHeader('bar'));

        // Psr7 consumption test
        // Represent the headers as a string
        foreach ($message->getHeaders() as $name => $values) {
            $this->assertEquals('foo: bar', $name . ": " . implode(", ", $values));
        }

        // Emit headers iteratively:
        foreach ($message->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $this->assertEquals('foo: bar', sprintf('%s: %s', $name, $value));
            }
        }
    }

    public function testReturnsSameInstanceWithoutChangingValue()
    {
        $message = $this->message->withHeader('foo', 'bar');

        $this->assertEquals(['bar'], $message->getHeader('foo'));
        $this->assertSame($message, $message->withHeader('foo', 'bar'));
    }

    public function testCaseInsensitiveHeaderAndLine()
    {
        $message = $this->message->withHeader('foo', ['bar', 'baz']);

        $this->assertCount(2, $message->getHeader('FOO'));
        $this->assertEquals('bar, baz', $message->getHeaderLine('foo'));
    }

    public function testWithAddedHeader()
    {
        $message = $this->message->withHeader('foo', 'bar');
        $this->assertEquals(['bar'], $message->getHeader('foo'));

        $message = $message->withAddedHeader('foo', 'baz');
        $this->assertEquals(['bar', 'baz'], $message->getHeader('foo'));
    }

    public function testWithoutHeader()
    {
        $message = $this->message->withHeader('foo', 'bar');

        // Make sure we have our header
        $this->assertEquals(['bar'], $message->getHeader('foo'));

        $message = $message->withoutHeader('foo');
        $this->assertCount(0, $message->getHeaders());
    }

    public function testMessageHandlesStream()
    {
        $message = $this->message->withBody(
            Mockery::mock(\Fyuze\Http\Message\Stream::class)
        );

        $this->assertNotSame($this->message, $message);
        $this->assertInstanceOf(\Fyuze\Http\Message\Stream::class, $message->getBody());

    }

    public function testCloneReturnsSameInstanceWithoutChangeInValues()
    {
        $message = $this->message->withProtocolVersion('1.1');
        $this->assertSame(
            $message,
            $message->withProtocolVersion('1.1')
        );
    }
}
