<?php

use Fyuze\Http\Request;
use Fyuze\Http\Response;
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

    public function testResolvesFromRootRoute()
    {
        $request = Request::create('/?test=1');
        $route = new Route('/', 'index', 'TestController@indexAction');

        $this->assertTrue($route->matches($request));
        $this->assertEquals('/?test=1', $request->getUri());
        $this->assertEquals('/', $request->getPath());
    }

    public function testResolvesWithIndexFile()
    {
        $request = Request::create('/index.php/?test=1');
        $route = new Route('/', 'index', 'TestController@indexAction');

        $this->assertTrue($route->matches($request));
        $this->assertEquals('/index.php/?test=1', $request->getUri());
        $this->assertEquals('/', $request->getPath());
    }

    public function testResolvesWithTrailingSlash() {
        $request = Request::create('/index.php/');
        $route = new Route('/', 'index', 'TestController@indexAction');

        $this->assertTrue($route->matches($request));
    }

    public function testControllerRoute()
    {
        $route = new Route('/', 'index', 'TestController@indexAction');

        $this->assertEquals('TestController', $route->getAction()[0]);
        $this->assertEquals('indexAction', $route->getAction()[1]);
    }


    public function testRouteWithTokens()
    {
        $route = new Route('/hello/{name}/{id}', 'index', 'TestController@helloAction');

        $this->assertEquals('TestController', $route->getAction()[0]);
        $this->assertEquals('helloAction', $route->getAction()[1]);
        $this->assertTrue($route->matches(Request::create('/hello/bob/1')));
        $this->assertFalse($route->matches(Request::create('/')));
        $this->assertFalse($route->matches(Request::create('/hello')));
    }

    public function testRouteWithOptionalTokens()
    {
        $route = new Route('/foo/{bar?}/{baz?}', 'index', 'TestController@helloAction');

        list($controller, $method) = $route->getAction();

        $this->assertEquals('TestController', $controller);
        $this->assertEquals('helloAction', $method);
        $this->assertTrue($route->matches(Request::create('/foo')));
        $this->assertTrue($route->matches(Request::create('/foo/bar')));
        $this->assertTrue($route->matches(Request::create('/foo/bar/baz')));
        $this->assertFalse($route->matches(Request::create('/')));
        $this->assertFalse($route->matches(Request::create('/foo/bar/baz/biz')));

        $this->assertEquals('Hello, Matthew!', call_user_func_array([new $controller, $method], ['Matthew']));
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
