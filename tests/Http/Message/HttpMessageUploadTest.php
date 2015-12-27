<?php

use Fyuze\Http\Message\Stream;
use Fyuze\Http\Message\Upload;

class HttpMessageUploadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $tmp;

    public function setUp()
    {
        $this->tmp = tempnam(sys_get_temp_dir(), 'fyuze');
    }

    public function constructorProviders()
    {
        return [
            [new Upload(fopen('php://temp', 'w+'))],
            [new Upload('php://temp')],
            ['php://temp']
        ];
    }

    /**
     * @dataProvider constructorProviders
     */
    public function testUploadTakesAppropriateFirstArugment($upload)
    {
        $upload = (is_object($upload)) ? $upload : new Upload($upload);
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $upload->getStream());
        $this->assertEquals(0, $upload->getSize());
        $this->assertEquals(0, $upload->getError());
        $this->assertNull($upload->getClientFilename());
        $this->assertNull($upload->getClientMediaType());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUploadThrowsErrorOnInvalidFirstArugment()
    {
        new Upload([]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetStreamThrowsExceptionAfterMoved()
    {
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

    /**
     * @expectedException RuntimeException
     */
    public function testMoveCannotBePerformedMoreThanOnce()
    {
        $upload = new Upload('php://temp');
        $upload->moveTo($this->tmp);
        $upload->moveTo($this->tmp);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNonWritableTargetsThrowsException()
    {
        (new Upload('php://temp'))
            ->moveTo(new Stream('php://temp'));
    }

}
