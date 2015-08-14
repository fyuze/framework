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
        $route = new Route('/foo/{bar?}/{baz?}', 'index', 'TestController@helloAction');

        list($controller, $method) = $route->getAction();

        $this->assertInstanceOf('TestController', $controller);
        $this->assertEquals('helloAction', $method);
        $this->assertTrue($route->matches('/foo'));
        $this->assertTrue($route->matches('/foo/bar'));
        $this->assertTrue($route->matches('/foo/bar/baz'));
        $this->assertFalse($route->matches('/'));
        $this->assertFalse($route->matches('/foo/bar/baz/biz'));

        $this->assertEquals('Hello, Matthew!', call_user_func_array([$controller, $method], ['Matthew']));
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
