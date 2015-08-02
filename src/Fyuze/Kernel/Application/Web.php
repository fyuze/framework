<?php
namespace Fyuze\Kernel\Application;

use Fyuze\Http\Kernel;
use Fyuze\Http\Response;
use Fyuze\Kernel\Fyuze;
use Fyuze\Http\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Web extends Fyuze
{
    /**
     * @return Response
     */
    public function boot($request = null)
    {
        $request = (null === $request) ? Request::createFromGlobals() : $request;
        $this->getContainer()->instance('request', $request);
        $routes = include $this->path . '/routes.php';

        $context = new RequestContext();
        $matcher = new UrlMatcher($routes, $context);
        $resolver = new ControllerResolver();

        $kernel = new Kernel($matcher, $resolver);
        $kernel = new HttpCache($kernel, new Store($this->path . '/app/cache'));
        return $kernel->handle($request);
    }
}
