<?php

use Fyuze\Kernel\Registry;

class KernelRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $registry = Registry::init();
        $registry->dump();
    }

    public function testSingleton()
    {
        $registry = Registry::init();
        $this->assertInstanceOf('Fyuze\Kernel\Registry', $registry);
        $this->assertSame($registry, Registry::init());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMakeInvalidMember()
    {
        $registry = Registry::init();
        $registry->make('this will fail');
    }

    public function testMakeClassFromString()
    {
        $registry = Registry::init();
        $mock = $registry->make('RegistryTestStub');
        $mock2 = $registry->make('RegistryTestStub');
        $this->assertInstanceOf('RegistryTestStub', $mock);
        $this->assertSame($mock, $mock2);
    }

    public function testMakeClassFromObject()
    {
        $registry = Registry::init();
        $stub = new RegistryTestStub();
        $stub->is_true = true;
        $mock = $registry->make($stub);
        $this->assertInstanceOf('RegistryTestStub', $mock);
    }
}


class RegistryTestStub
{
    public $is_true = false;
}
