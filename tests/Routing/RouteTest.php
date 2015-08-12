<?php

use Fyuze\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{

    public function testBasicRoutes()
    {
        $route = new Route('/', 'index', function () { return 'Hello'; });

        $this->assertEquals('/', $route->getUri());
        $this->assertEquals('Hello', call_user_func($route->getAction()));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNonClosuresThrowExceptions()
    {
        $route = new Route('/', 'index', 'TestController@indexAction');
        $route->getAction();
    }
}
