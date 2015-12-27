<?php
namespace Fyuze\Routing;

use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

class Route
{
    /**
     * @var
     */
    protected $uri;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $action;

    /**
     * @param $uri
     * @param $name
     * @param $action
     */
    public function __construct($uri, $name, $action)
    {
        $this->uri = $uri;
        $this->name = $name;
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }


    /**
     * @throws InvalidArgumentException
     * @return mixed Closure|Controller
     */
    public function getAction()
    {
        if ($this->action instanceof Closure) {

            return $this->action;
        }

        list($controller, $method) = explode('@', $this->action);

        if (!class_exists($controller)) {
            throw new InvalidArgumentException('Invalid controller specified');
        }

        return [$controller, $method];
    }

    /**
     * Check if route matches given url
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function matches(ServerRequestInterface $request)
    {
        return (new Matcher($request, $this))->resolves();
    }
}
