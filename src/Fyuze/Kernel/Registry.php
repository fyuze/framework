<?php
namespace Fyuze\Kernel;

use ReflectionClass;

class Registry
{
    /**
     * Singleton instance
     *
     * @var static
     */
    protected static $instance = null;

    /**
     * Registerd services
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
     * @param $member
     * @return mixed|void
     */
    public function make($member)
    {
        $key = is_object($member) ? get_class($member) : $member;

        if (array_key_exists($key, $this->members)) {

            return $this->members[$key];
        }

        if(is_object($member)) {
            return $this->members[$key] = $member;
        }

        return $this->create($key);

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
     * @return mixed
     */
    protected function create($class)
    {
        return $this->members[$class] = $this->resolve($class);
    }

    /**
     * @param $class
     * @return object
     */
    protected function resolve($class)
    {
        $reflection = new ReflectionClass($class);

        if(!$constructor = $reflection->getConstructor()) {

            return new $class;
        }

        $params = $constructor->getParameters();

        /** @var \ReflectionParameter $param */
        foreach(array_filter($params, $this->getParams()) as $param) {
            array_unshift(
                $params,
                $this->make($param->getClass()->getName())
            );
        }

        return $reflection->newInstanceArgs($params);
    }

    /**
     * @return Closure
     */
    protected function getParams()
    {
        return function ($param) {
            return $param->getClass();
        };
    }
}
