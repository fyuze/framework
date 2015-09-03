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
     * @expectedException ReflectionException
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
        $this->assertSame($mock, $registry->make('RegistryTestStub'));
    }

    public function testMakeClassFromObject()
    {
        $registry = Registry::init();
        $stub = new RegistryTestStub();
        $stub->is_true = true;
        $mock = $registry->make($stub);
        $this->assertSame($stub, $mock);
        $this->assertTrue($mock->is_true);
    }

    public function testAutoResolvesTypeHints()
    {
        $registry = Registry::init();
        $instance = $registry->make('RegistryWithDependencyStub');

        $this->assertInstanceOf('RegistryDependencyStub', $instance->getStub());
    }
}


class RegistryTestStub
{
}

class RegistryDependencyStub
{
}

class RegistryWithDependencyStub
{
    public function __construct(RegistryDependencyStub $stub)
    {
        $this->stub = $stub;
    }

    public function getStub()
    {
        return $this->stub;
    }
}
