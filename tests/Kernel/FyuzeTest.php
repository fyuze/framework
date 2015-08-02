<?php

use Fyuze\Kernel\Application\Web;

class KernelFyuzeTest extends \PHPUnit_Framework_TestCase
{
    public function testWebApplication()
    {
        $app = (new Web(realpath(__DIR__ . '/../mocks')));
        $this->assertInstanceOf('Illuminate\Container\Container', $app->getContainer());
    }

    public function testCliApplication()
    {
        //$app = (new Console(__DIR__))->boot();
        //$this->assertTrue($app->isCli());
    }

    public function testAppHasConfig()
    {

    }
}
