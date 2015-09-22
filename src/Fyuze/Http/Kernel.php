<?php
namespace Fyuze\Http;

use Closure;
use ReflectionClass;
use ReflectionParameter;
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

            $body = $this->resolve($route->getAction(), $request->getParams());

            $response = new Response($body);

        } catch (NotFoundException $e) {

            $response = new Response('<body>Not Found</body>', 404);

        } catch (\Exception $e) {

            $response = new Response(sprintf('<body>An unkown error has occurred: %s</body>', $e->getMessage()), 500);
        }

        $this->registry->add('response', $response);

        return $this->registry->make('response');
    }

    /**
     * Method injection resolver
     *
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
        return function (ReflectionParameter $param) {
            return $param->getClass();
        };
    }
}
