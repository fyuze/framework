<?php
namespace Fyuze\Routing;

use Fyuze\Http\Exception\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;

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
     * @param ServerRequestInterface $request
     * @return Route
     * @throws NotFoundException
     */
    public function resolve(ServerRequestInterface $request)
    {
        $route = $this->match($request);

        if (count($route) === 0) {
            throw new NotFoundException('Page not found');
        }

        return reset($route);
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function match(ServerRequestInterface $request)
    {
        return array_filter($this->routes->getRoutes(), function (Route $route) use ($request) {
            $match = $route->matches($request);
            if(false !== $match) {
                return $route->setParams($match);
            }
        });
    }
}
