<?php
use Fyuze\Debug\Collectors\Response;

class ResponseCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectorGetsResponse()
    {
        $mock = Mockery::mock('Fyuze\Http\Response');
        $mock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $collector = new Response($mock);

        $this->assertArrayHasKey('title', $collector->tab());
        $this->assertEquals('200', $collector->tab()['title']);
    }
}
