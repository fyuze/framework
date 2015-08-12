<?php
namespace Fyuze\Routing;

class Collection
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param $route
     * @param $action
     * @param null $name
     * @return Collection
     */
    public function get($route, $name, $action, $options = [])
    {
        return $this->add($route, $name, $action, array_merge(['method' => 'GET'], $options));
    }

    /**
     * @param $route
     * @param $action
     * @param null $name
     * @return Collection
     */
    public function post($route, $name, $action, $options = [])
    {
        return $this->add($route, $name, $action, array_merge(['method' => 'POST'], $options));
    }

    /**
     * @param $methods
     * @param $route
     * @param $action
     * @param null $name
     * @return $this
     */
    protected function add($route, $name, $action, $options = [])
    {
        $this->routes[] = new Route($route, $name, $action, $options);

        return $this;
    }
}
