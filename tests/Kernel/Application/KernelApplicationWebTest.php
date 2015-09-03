<?php

use Fyuze\Http\Response;

class KernelApplicationWebTest extends PHPUnit_Framework_TestCase
{
    public function testWebApplicationBootsSuccessfully()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $app->getContainer()->make('Fyuze\Routing\Collection')->get('/', 'index', 'HomeController@indexAction');
        $response = $app->boot();

        $app->getContainer()->dump();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/', $response->getBody());
    }

    public function testWebApplicationThrows404s()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $_SERVER['REQUEST_URI'] = '/foobar';
        $response = $app->boot();
        unset($_SERVER['REQUEST_URI']);

        $app->getContainer()->dump();

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


class HomeController {
    protected $registry;
    public function __construct(\Fyuze\Http\Request $registry) {
        $this->registry = $registry;
    }
    public function indexAction(\Fyuze\Http\Request $request) {
        return new Response($request->getPath());
    }
}
