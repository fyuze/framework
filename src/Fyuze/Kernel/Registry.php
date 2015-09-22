<?php
namespace Fyuze\Kernel;

use Closure;
use ReflectionClass;
use ReflectionParameter;

class Registry
{
    /**
     * Singleton instance
     *
     * @var static
     */
    protected static $instance = null;

    /**
     * Registered services
     *
     * @var array
     */
    protected $members = [];

    /**
     * Initialize object
     *
     * @return static
     */
    public static function init()
    {
        if (self::$instance === null) {
            return static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @param string $alias
     * @param mixed $member
     */
    public function add($alias, $member)
    {
        $this->members[$alias] = $member;
    }

    /**
     * @param $alias
     * @return bool
     */
    public function has($alias)
    {
        return array_key_exists($alias, $this->members);
    }

    /**
     * @param string $member
     * @return mixed|void
     */
    public function make($member)
    {
        $key = is_object($member) ? get_class($member) : $member;

        if (array_key_exists($key, $this->members)) {

            return $this->build($this->members[$member]);
        }

        if (is_object($member)) {

            return $this->members[$key] = $member;
        }

        return $this->locate($key, $member);
    }

    /**
     *
     */
    public static function dump()
    {
        static::$instance->members = [];
    }

    /**
     * @param $class
     * @return object
     */
    protected function resolve($class)
    {
        $reflection = new ReflectionClass($class);

        if (!$constructor = $reflection->getConstructor()) {

            return new $class;
        }

        $params = array_filter($constructor->getParameters(), $this->getParams());

        /** @var \ReflectionParameter $param */
        foreach ($params as &$param) {
            $param = $this->make($param->getClass()->getName());
        }

        return $reflection->newInstanceArgs($params);
    }

    /**
     * @param $member
     * @return mixed
     */
    protected function build(&$member)
    {
        if ($member instanceof Closure) {
            return $member = $member($this);
        }

        return $member;
    }

    /**
     * @param $key
     * @param $member
     * @return mixed|object
     */
    protected function locate($key, $member)
    {
        $aliases = array_filter($this->members, function (&$n) use ($member) {
            if ($n instanceof Closure) {
                return $n($this) instanceof $member;
            }
            return $n instanceof $member;
        });

        if (count($aliases)) {
            $alias = reset($aliases);
            if ($alias instanceof Closure) {
                return $alias($this);
            }
            return $alias;
        }

        return $this->members[$key] = $this->resolve($key);
    }

    /**
     * @return Closure
     */
    protected function getParams()
    {
        return function (ReflectionParameter $param) {
            return $param->getClass();
        };
    }
}
