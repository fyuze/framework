<?php

use Fyuze\Http\Message\Stream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HttpMessageStreamTest extends TestCase
{
    /**
     * @var Stream
     */
    protected $stream;

    /**
     * @var
     */
    protected $tmp;

    public function setUp(): void
    {
        $this->stream = new Stream('php://memory', 'w+');
        $this->tmp = tempnam(sys_get_temp_dir(), 'fyuze');
    }

    public static function getReadableModes()
    {
        return [
            ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        ];
    }

    public static function getWriteableModes()
    {
        return [
            ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
        ];
    }

    public function testToStringReturnsContentWhenReadable()
    {
        $stream = new Stream('php://memory', 'w+');
        $stream->write('foobar');
        $this->assertEquals('foobar', (string)$stream);
    }

    public function testToStringReturnsEmptyOnWritableOnlyStream()
    {
        $stream = new Stream($this->tmp, 'w');
        $stream->write('foobar');
        $this->assertEquals('', (string)$stream);
    }

    public function testToStringTurnsExceptionsIntoEmptyString()
    {
        $stream = new Stream($this->tmp, 'w');
        $this->assertEmpty((string)$stream);
    }

    public function testCloseKillsStream()
    {
        $stream = new Stream('php://memory');
        $resource = fopen('php://memory', 'r');
        $stream->attach($resource);
        $stream->close();
        $this->assertFalse(is_resource($resource));
    }

    public function testCloseFailsWithoutResource()
    {
        $this->stream->detach();
        $this->assertNull($this->stream->close());
    }

    public function testAttachFailsWithoutResource()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->stream->attach('');
    }

    public function testDetachReturnsOriginalResource()
    {
        $resource = fopen('php://temp', 'r');

        $this->stream->attach($resource);
        $this->assertSame($resource, $this->stream->detach());
    }

    public function testGetSizeOfStream()
    {
        $stream = new Stream('php://memory', 'w+');
        $this->assertEquals(0, $stream->getSize());
        $stream->write('foobar');
        $this->assertEquals(6, $stream->getSize());
    }

    public function testGetSizeReturnsNullWhenStreamDetached()
    {
        $stream = new Stream('php://memory', 'w+');
        $stream->detach();
        $this->assertNull($stream->getSize());
    }

    public function testTellGivesCorrectPosition()
    {
        $this->stream->rewind();
        $this->assertEquals(0, $this->stream->tell());
        $this->stream->write('foobar');
        $this->stream->seek(5);
        $this->assertEquals(5, $this->stream->tell());
    }

    public function testTellFailsWhenStreamDetached()
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream('php://memory');
        $stream->detach();
        $stream->tell();
    }

    public function testEofRetrunsFalseWhenNotEndOfStream()
    {
        $stream = new Stream('php://memory', 'w+');
        $stream->write('foobar');
        $stream->rewind();
        $this->assertFalse($stream->eof());
    }

    public function testEofRetrunsTrueWhenEndOfStream()
    {
        $stream = new Stream('php://memory', 'w+');
        $stream->write('foobar');
        while (!$stream->eof()) {
            $stream->read(4096);
        }
        $this->assertTrue($stream->eof());
    }

    public function testEofRetrunsTrueIfNoResourceAvailable()
    {
        $stream = new Stream('php://memory');
        $stream->detach();
        $this->assertTrue($stream->eof());
    }

    public function testIsSeekableReturnsTrueForValidStream()
    {
        $this->assertTrue($this->stream->isSeekable());
    }

    public function testIsSeekableReturnsFalseForInvalidStream()
    {
        $stream = new Stream('php://memory');
        $stream->detach();
        $this->assertFalse($stream->isSeekable());
    }

    public function testSeekAppropriateMovesPointer()
    {
        $this->stream->write('foobar');
        $this->stream->seek(2);
        $this->assertTrue($this->stream->seek(2));
        $this->assertEquals(2, $this->stream->tell());
    }

    public function testSeekThrowsExceptionWhenDetached()
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream('php://memory');
        $stream->detach();
        $stream->seek(1);
    }

    public function testRewindResetsPointer()
    {
        $this->stream->write('foobar');
        $this->stream->seek(2);
        $this->assertEquals(2, $this->stream->tell());
        $this->stream->rewind();
        $this->assertEquals(0, $this->stream->tell());
    }

    /**
     * @param $mode
     */
    #[DataProvider('getReadableModes')]
    public function testValidReadableModesReturnTrue($mode)
    {
        $stream = new Stream('php://memory', $mode);

        $this->assertTrue($stream->isReadable());
    }

    /**
     * @param $mode
     */
    #[DataProvider('getWriteableModes')]
    public function testWriteableModes($mode)
    {
        $stream = new Stream('php://memory', $mode);

        $this->assertTrue($stream->isWritable());
    }

    public function testReadStreamReturnsFalseForIsWritable()
    {
        $stream = new Stream('php://memory');
        $this->assertFalse($stream->isWritable());
    }

    public function testWriteToStream()
    {
        $this->assertEquals(6, $this->stream->write('foobar'));
    }

    public function testWriteOnReadStreamThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream('php://memory');
        $stream->write('foobar');
    }

    public function testReadReturnsContentOnReadableStream()
    {
        $this->stream->write('foobar');
        $this->stream->rewind();

        $this->assertEquals('foobar', $this->stream->read(100));
    }

    public function testReadThrowsExceptionIfNotReadable()
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream($this->tmp, 'w');
        $stream->read(100);
    }

    public function testGetsContentsFromStream()
    {
        $stream = new Stream('php://memory', 'w+');
        $stream->write('foobar');
        $stream->seek(0);
        $this->assertEquals(6, $stream->getSize());
        $this->assertEquals('foobar', $stream->getContents());
    }

    public function testGetContentsThrowsExceptionIfNotReadable()
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream($this->tmp, 'w');
        $stream->getContents();
    }

    public function testGetsAllMetadataWithoutKey()
    {
        $meta = $this->stream->getMetadata();
        $this->assertArrayHasKey('wrapper_type', $meta);
        $this->assertArrayHasKey('stream_type', $meta);
        $this->assertArrayHasKey('mode', $meta);
        $this->assertArrayHasKey('seekable', $meta);
    }

    public function testGetsSpecificMetadata()
    {
        $this->assertEquals('php://memory', $this->stream->getMetadata('uri'));
    }

    public function testGetMetadataReturnsNullWithInvalidKey()
    {
        $this->assertNull($this->stream->getMetadata('foobar'));
    }
}
