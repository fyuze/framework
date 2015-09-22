<?php

use Fyuze\Kernel\Registry;

class KernelRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $registry = Registry::init();
        $registry->dump();
    }

    public function testRegistryIsSingleton()
    {
        $registry = Registry::init();
        $this->assertInstanceOf('Fyuze\Kernel\Registry', $registry);
        $this->assertSame($registry, Registry::init());
    }

    public function testRegistryHasMethod() {
        $registry = Registry::init();
        $this->assertFalse($registry->has('foo'));
        $registry->add('foo', function() { return 'bar'; });
        $this->assertTrue($registry->has('foo'));
    }

    public function testRegistryCreatesMemberWithAlias()
    {
        $registry = Registry::init();
        $registry->add('test', function ($registry) {
            $class = new StdClass;
            $class->foo = 'bar';
            return $class;
        });

        $obj = $registry->make('test');
        $this->assertEquals('bar', $obj->foo);
    }

    /**
     * When we type hint a class, e.g Fyuze\Database\Db
     * and have it bound to the key 'db'
     * we still need to auto resolve from that key
     */
    public function testRegistryAutoResolvesBoundViaClosure()
    {
        $registry = Registry::init();

        $foo = new Foo;

        $registry->add('abcd', function ($registry) use ($foo) {
            return $foo;
        });

        // Foo is type hinted and should resolve the instance we've provided
        $test = $registry->make('RegistryWithOneDependency');
        $this->assertSame($foo, $test->getFoo());
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
        $this->assertInstanceOf('Foo', $registry->make('Foo'));
    }

    public function testMakeClassFromObject()
    {
        $registry = Registry::init();
        $stub = new Foo();
        $stub->is_true = true;
        $mock = $registry->make($stub);
        $this->assertSame($stub, $mock);
        $this->assertTrue($mock->is_true);
    }

    public function testAutoResolvesSingleTypeHint()
    {
        $registry = Registry::init();
        $instance = $registry->make('RegistryWithOneDependency');

        $this->assertInstanceOf('Foo', $instance->getFoo());
    }

    public function testAutoResolvesMultipleTypeHints()
    {
        $registry = Registry::init();
        $instance = $registry->make('RegistryWithMultipleDependencies');

        $this->assertInstanceOf('Foo', $instance->getFoo());
        $this->assertInstanceOf('Bar', $instance->getBar());
    }
}


class Foo
{
}

class Bar
{
}

class RegistryWithOneDependency
{
    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}

class RegistryWithMultipleDependencies
{
    public function __construct(Foo $foo, Bar $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
