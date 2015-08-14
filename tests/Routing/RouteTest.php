<?php

use Fyuze\Routing\Route;
use Fyuze\Http\Response;

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


    public function testRouteWithTokens()
    {
        $route = new Route('/hello/{name}/{id}', 'index', 'TestController@helloAction');

        $this->assertInstanceOf('TestController', $route->getAction()[0]);
        $this->assertEquals('helloAction', $route->getAction()[1]);
        $this->assertTrue($route->matches('/hello/bob/1'));
        $this->assertFalse($route->matches('/'));
        $this->assertFalse($route->matches('/hello'));
    }

    public function testRouteWithOptionalTokens()
    {
        $route = new Route('/hello/{name?}/{id?}', 'index', 'TestController@helloAction');

        $this->assertInstanceOf('TestController', $route->getAction()[0]);
        $this->assertEquals('helloAction', $route->getAction()[1]);
        $this->assertTrue($route->matches('/hello'));
        $this->assertTrue($route->matches('/hello/test'));
        $this->assertTrue($route->matches('/hello/test/test'));
        $this->assertFalse($route->matches('/'));
        $this->assertFalse($route->matches('/hello/test/test/test'));
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
    public function indexAction() {}
    public function helloAction($name) {
        return new Response(sprintf('Hello, %s!', $name));
    }
}
