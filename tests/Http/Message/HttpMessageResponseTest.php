<?php
use Fyuze\Http\Message\Response;
use PHPUnit\Framework\TestCase;

class HttpMessageResponseTest extends TestCase
{
    public function testWithStatusReturnsNewInstance()
    {
        $response = new Response();
        $this->assertNotSame($response, $response->withStatus(404));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusThrowsExceptionOnInvalidCode()
    {
        (new Response)->withStatus(999);
    }

    public function testReasonPhraseReturnsDefaultMessage() {
        $response = (new Response)->withStatus(200);
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function testReasonPhaseReturnsCustomMessage() {
        $response = (new Response)->withStatus(200, 'FOOYA');
        $this->assertEquals('FOOYA', $response->getReasonPhrase());
    }
}
