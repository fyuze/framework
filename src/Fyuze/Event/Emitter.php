<?php
namespace Fyuze\Event;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class Emitter
{
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @param $name
     * @param \Closure $closure
     */
    public function listen($name, \Closure $closure)
    {
        $this->events[$name][] = $closure;
    }

    /**
     * @param $name
     * @return bool
     */
    public function drop($name)
    {
        if (!$this->has($name)) {
            return false;
        }

        unset($this->events[$name]);

        return true;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->events);
    }

    /**
     * @param $name
     * @param array $params
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function emit($name, array $params = [])
    {
        foreach ($this->locate($name) as $event) {

            call_user_func_array(
                $event,
                $params
            );
        }
    }

    /**
     * @param $name
     * @return array
     * @throws InvalidArgumentException
     */
    public function locate($name)
    {
        if (!$this->has($name)) {

            $message = sprintf('Event %s has not been set', $name);

            if ($this->logger !== null) {

                $this->logger->warning($message);

                return [];
            }

            throw new InvalidArgumentException($message);
        }

        return $this->events[$name];
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
