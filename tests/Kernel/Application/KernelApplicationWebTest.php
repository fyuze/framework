<?php

use Fyuze\Http\Response;

class KernelApplicationWebTest extends PHPUnit_Framework_TestCase
{
    public function testWebApplicationBootsSuccessfully()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $app->getContainer()->make('Fyuze\Routing\Collection')->get('/', 'index', function () {
            return new Response('Hello, World!');
        });
        $response = $app->boot();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
       // $this->assertEquals('Hi', $response->getBody());
    }

    public function testWebApplicationThrows404s()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $_SERVER['REQUEST_URI'] = '/foobar';
        $response = $app->boot();
        unset($_SERVER['REQUEST_URI']);

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testWebApplicationThrows500s()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $app->getContainer()->make('Fyuze\Routing\Collection')->get('/', 'error', function () {
            throw new Exception('stuff broke');
        });
        $response = $app->boot();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('An unkown error has occurred: stuff broke', $response->getBody());
    }
}
