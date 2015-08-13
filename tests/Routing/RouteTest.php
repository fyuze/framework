<?php

use Fyuze\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{

    public function testClosureRoute()
    {
        $route = new Route('/', 'index', function () { return 'Hello'; });

        $this->assertEquals('/', $route->getUri());
        $this->assertInstanceOf('Closure', $route->getAction());
        $this->assertEquals('Hello', call_user_func($route->getAction()));
    }

    public function testControllerRoute()
    {
        $route = new Route('/', 'index', 'TestController@indexAction');

        $this->assertInstanceOf('TestController', $route->getAction()[0]);
        $this->assertEquals('indexAction', $route->getAction()[1]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadControllerRoute()
    {
        $route = new Route('/', 'index', 'FakeController@indexAction');
        $route->getAction();
    }
}


class TestController {
    public function indexAction() {

    }
}
