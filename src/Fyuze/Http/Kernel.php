<?php
namespace Fyuze\Http;

use Closure;
use ReflectionClass;
use Fyuze\Http\Exception\NotFoundException;
use Fyuze\Kernel\Registry;
use Fyuze\Routing\Router;

class Kernel
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     *
     * @var Router
     */
    protected $router;

    /**
     * @param Registry $registry
     * @param Router $router
     */
    public function __construct(Registry $registry, Router $router)
    {
        $this->registry = $registry;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function handle(Request $request)
    {
        try {

            $route = $this->router->resolve($request);

            return $this->resolve($route->getAction(), $request->getParams());

        } catch (NotFoundException $e) {

            return new Response('Not Found', 404);

        } catch (\Exception $e) {

            return new Response(sprintf('An unkown error has occurred: %s', $e->getMessage()), 500);
        }
    }

    /**
     * @param $action
     * @param $params
     * @return mixed
     */
    protected function resolve($action, $params)
    {
        if ($action instanceof Closure) {

            return $action($params);
        }

        list($controller, $method) = $action;

        $reflect = new ReflectionClass($controller);

        foreach (array_filter($reflect->getMethod($method)->getParameters(), $this->getParams()) as $param) {
            array_unshift($params, $this->registry->make(
                $param->getClass()->getName()
            ));
        }

        return $reflect->getMethod($method)->invokeArgs(
            $this->registry->make($controller),
            $params
        );
    }

    /**
     * @return Closure
     */
    protected function getParams()
    {
        return function ($param) {
            return $param->getClass();
        };
    }
}
