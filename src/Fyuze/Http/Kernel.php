<?php
namespace Fyuze\Http;

use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Kernel
{
    /**
     *
     * @var UrlMatcher
     */
    protected $matcher;

    /**
     *
     * @var ControllerResolver
     */
    protected $resolver;

    /**
     *
     * @param UrlMatcher $matcher
     * @param ControllerResolver $resolver
     */
    public function __construct(UrlMatcher $matcher, ControllerResolver $resolver)
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
    }

    /**
     *
     * @param Request $request
     * @return mixed|Response
     */
    public function handle(Request $request)
    {
        $this->matcher->getContext()->fromRequest($request);

        try {

            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->resolver->getController($request);
            $arguments = $this->resolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);

        } catch (ResourceNotFoundException $e) {

            return new Response('Not Found', 404);

        } catch (\Exception $e) {

            return new Response('An error occurred', 500);
        }
    }
}
