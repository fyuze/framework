<?php
namespace Fyuze\Kernel;

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
            return self::$instance = new static;
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

        // Member is already bound, retrieve it
        if (array_key_exists($key, $this->members)) {
            return $this->members[$key];
        }

        return $this->add($member);
    }

    /**
     * @param $instance
     * @return mixed|void
     * @throws \InvalidArgumentException
     */
    public function add($instance)
    {
        if (is_string($instance) && class_exists($instance)) {
            return $this->create($instance);
        }

        if (is_object($instance)) {
            return $this->register($instance);
        }

        throw new \InvalidArgumentException('You must provide a valid class name or object.');
    }

    /**
     *
     */
    public static function dump()
    {
        $instance = static::$instance;
        unset($instance->members);
        $instance->members = [];
    }

    /**
     * @param $class
     * @return mixed
     */
    protected function create($class)
    {
        return $this->members[$class] = new $class;
    }

    /**
     * @param $instance
     * @return mixed
     */
    protected function register($instance)
    {
        return $this->members[get_class($instance)] = $instance;
    }
}
