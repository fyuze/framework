<?php
namespace Fyuze\Kernel\Application;

use Fyuze\Http\Kernel;
use Fyuze\Http\Response;
use Fyuze\Kernel\Fyuze;
use Fyuze\Http\Request;
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

        $kernel = new Kernel(new Router($routes));
//        if($this->config->get('app.debug')) {
//            $kernel = new HttpCache($kernel);
//        }

        return $kernel->handle(Request::create());
    }
}
