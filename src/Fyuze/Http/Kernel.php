<?php
namespace Fyuze\Http;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * @param ServerRequestInterface $request
     * @return Response|mixed
     */
    public function handle(ServerRequestInterface $request)
    {
        try {

            $route = $this->router->resolve($request);

            $response = $this->resolve($route->getAction(), $route->getQueryParams());
            if(false === $response instanceof ResponseInterface) {
                $response = Response::create($response);
            }


        } catch (NotFoundException $e) {

            $response = Response::create('<body>Not Found</body>', 404);

        } catch (\Exception $e) {

            $response = Response::create(
                sprintf('<body>An unknown error has occurred: %s</body>', $e->getMessage()),
                500
            );
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
                (string) $param->getType()
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
            return (string) $param->getType();
        };
    }
}
