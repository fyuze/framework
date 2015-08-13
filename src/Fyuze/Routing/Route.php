<?php
namespace Fyuze\Routing;

use Closure;
use InvalidArgumentException;

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
     * @param array $options
     */
    public function __construct($uri, $name, $action, $options = [])
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

        return [new $controller, $method];
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [];
    }
}
