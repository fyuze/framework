<?php
namespace Fyuze\Http;

use Fyuze\Http\Exception\NotFoundException;
use Fyuze\Routing\Router;

class Kernel
{
    /**
     *
     * @var Router
     */
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
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

            return call_user_func_array($route->getAction(), $route->getParams());

        } catch (NotFoundException $e) {

            return new Response('Not Found', 404);

        } catch (\Exception $e) {

            return new Response('An error occurred', 500);
        }
    }
}
