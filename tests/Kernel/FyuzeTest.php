<?php

use Fyuze\Kernel\Application\Web;

class KernelFyuzeTest extends \PHPUnit_Framework_TestCase
{
    public function testWebApplication()
    {
        $app = (new Web(realpath(__DIR__ . '/../mocks')));
        $app->boot();
        $this->assertInstanceOf('Fyuze\Kernel\Registry', $app->getContainer());
    }

    public function testCliApplication()
    {
        //$app = (new Console(__DIR__))->boot();
        //$this->assertTrue($app->isCli());
    }

    public function testLoggingService()
    {
        $app = (new Web(realpath(__DIR__ . '/../mocks')));
        $app->getContainer()->make('Fyuze\Config\Config')->set('app.error_handler.log_errors', false);

        $app->boot();

        $this->assertInstanceOf('Fyuze\Kernel\Registry', $app->getContainer());
    }
}
