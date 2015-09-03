<?php
namespace Fyuze\Kernel\Application;

use Fyuze\Http\Kernel;
use Fyuze\Http\Response;
use Fyuze\Http\Request;
use Fyuze\Kernel\Fyuze;
use Fyuze\Routing\Router;

class Web extends Fyuze
{
    /**
     * @return Response
     */
    public function boot()
    {
        // Get system is booting, load routes
        $routes = $this->loadRoutes();

        $kernel = new Kernel($this->getContainer(), new Router($routes));

        return $kernel->handle($this->container->make(Request::create()));
    }
}
