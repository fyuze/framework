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

        $this->container->add('request', function () {
            return Request::create();
        });

        $response = $kernel->handle($this->container->make('request'));

        foreach ($this->services as $service) {
            if (method_exists($service, 'bootstrap')) {
                $service->bootstrap();
            }
        }

        return $response;
    }
}
