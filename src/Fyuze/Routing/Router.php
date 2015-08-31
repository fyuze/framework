<?php
namespace Fyuze\Routing;

use Fyuze\Http\Request;
use Fyuze\Http\Exception\NotFoundException;

class Router
{
    /**
     * @var Collection
     */
    protected $routes;

    /**
     * @param Collection $routes
     */
    public function __construct(Collection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param Request $request
     * @return Route
     * @throws NotFoundException
     */
    public function resolve(Request $request)
    {
        $route = $this->match($request);

        if (count($route) === 0) {
            throw new NotFoundException;
        }

        return reset($route);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function match(Request $request)
    {
        return array_filter($this->routes->getRoutes(), function (Route $route) use ($request) {
            return $route->matches($request);
        });
    }
}
