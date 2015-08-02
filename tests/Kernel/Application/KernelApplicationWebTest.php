<?php

class KernelApplicationWebTest extends PHPUnit_Framework_TestCase
{
    public function testWebApplicationBootsSuccessfully()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $response = $app->boot();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hi', $response->getContent());
    }

    public function testWebApplicationThrows404s()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);

        /** @var \Fyuze\Http\Request $request */
        $request = $app->getRegistry()->make('Fyuze\Http\Request');
        $app->getRegistry()->make($request->create('/foo'));
        $response = $app->boot();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testWebApplicationThrows500s()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);

        /** @var \Fyuze\Http\Request $request */
        $request = $app->getRegistry()->make('Fyuze\Http\Request');
        $app->getRegistry()->make($request->create('/throwD'));
        $response = $app->boot();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
