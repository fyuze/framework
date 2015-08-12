<?php
namespace Fyuze\Routing;

use Closure;

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
     * @return Closure
     */
    public function getAction()
    {
        if ($this->action instanceof Closure) {

            return $this->action;
        }

        throw new \RuntimeException('Only closures can be used for route actions for now.');
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [];
    }
}
