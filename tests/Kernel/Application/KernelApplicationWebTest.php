<?php

class KernelApplicationWebTest extends PHPUnit_Framework_TestCase
{
    public function testWebApplicationBoots()
    {
        $path = realpath(__DIR__ . '/../../mocks');
        $app = new \Fyuze\Kernel\Application\Web($path);
        $response = $app->boot();

        $this->assertInstanceOf('Fyuze\Http\Response', $response);
    }
}
