<?php

use Fyuze\Http\Message\Stream;
use Fyuze\Http\Message\Upload;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HttpMessageUploadTest extends TestCase
{
    /**
     * @var string
     */
    protected $tmp;

    public function setUp(): void
    {
        $this->tmp = tempnam(sys_get_temp_dir(), 'fyuze');
    }

    public static function constructorProviders()
    {
        return [
            [new Upload(fopen('php://temp', 'w+'))],
            [new Upload('php://temp')],
            ['php://temp']
        ];
    }

    #[DataProvider('constructorProviders')]
    public function testUploadTakesAppropriateFirstArugment($upload)
    {
        $upload = (is_object($upload)) ? $upload : new Upload($upload);
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $upload->getStream());
        $this->assertEquals(0, $upload->getSize());
        $this->assertEquals(0, $upload->getError());
        $this->assertNull($upload->getClientFilename());
        $this->assertNull($upload->getClientMediaType());
    }

    public function testUploadThrowsErrorOnInvalidFirstArugment()
    {
        $this->expectException(InvalidArgumentException::class);
        new Upload([]);
    }

    public function testGetStreamThrowsExceptionAfterMoved()
    {
        $this->expectException(RuntimeException::class);
        $upload = new Upload('php://temp');
        $upload->moveTo($this->tmp);
        $upload->getStream();
    }


    public function testMovesFileToDesignatedPath()
    {
        $stream = new Stream('php://temp', 'wb+');
        $stream->write('Foo bar!');

        $target = $this->tmp;

        $upload = new Upload($stream);
        $upload->moveTo($target);

        $this->assertTrue(file_exists($target));
        $this->assertEquals(
            $stream->__toString(),
            file_get_contents($target)
        );
    }

    public function testMoveCannotBePerformedMoreThanOnce()
    {
        $this->expectException(RuntimeException::class);
        $upload = new Upload('php://temp');
        $upload->moveTo($this->tmp);
        $upload->moveTo($this->tmp);
    }

    public function testNonWritableTargetsThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Upload('php://temp'))
            ->moveTo(new Stream('php://temp'));
    }

}
