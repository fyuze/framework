<?php
namespace Fyuze\Kernel\Application;

use Fyuze\Http\Kernel;
use Fyuze\Http\Request;
use Fyuze\Http\Response;
use Fyuze\Kernel\Fyuze;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Web extends Fyuze
{
    /**
     * @return Response
     */
    public function boot()
    {
        $request = Request::createFromGlobals();
        $routes = include $this->path . '/routes.php';

        $context = new RequestContext();
        $matcher = new UrlMatcher($routes, $context);
        $resolver = new ControllerResolver();

        $kernel = new Kernel($matcher, $resolver);
        $response = $kernel->handle($request);

        return $response->send();
    }
}
