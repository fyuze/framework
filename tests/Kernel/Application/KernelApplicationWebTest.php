<?php

use Fyuze\Http\Response;
use Fyuze\Http\Exception\NotFoundException;

class KernelApplicationWebTest extends PHPUnit_Framework_TestCase
{
    public function testWebApplicationBootsSuccessfully()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $app->getContainer()->make('routes')->get('/', 'index', 'HomeController@indexAction');
        $response = $app->boot();

        $app->getContainer()->dump();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foobar', (string) $response->getBody());
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
        $this->assertEquals('<body>Not Found</body>', (string) $response->getBody());
    }

    public function testWebApplicationThrows500s()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $app->getContainer()->make('routes')->get('/', 'error', function () {
            throw new Exception('stuff broke');
        });
        $response = $app->boot();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('<body>An unkown error has occurred: stuff broke</body>', $response->getBody());
    }
}


class HomeController {
    protected $registry;
    public function __construct(\Fyuze\Kernel\Registry $registry) {
        $this->registry = $registry;
    }
    public function indexAction(\Fyuze\Http\Request $request) {

        return 'foobar';
    }
}
