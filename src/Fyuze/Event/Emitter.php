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
        $count = 0;

        foreach ($this->locate($name) as $event) {

            $count++;

            call_user_func_array(
                $event,
                $params
            );
        }

        if($count > 0) {

            $this->log(sprintf('Event: %s has been called. %d listeners were fired', $name, $count));
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

            $this->log(sprintf('Event %s called but no listeners were registered', $name));

            return [];
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

    /**
     * @param $message
     * @param string $level
     * @return bool
     */
    protected function log($message, $level = 'notice')
    {
        if ($this->logger !== null) {

            $this->logger->log($level, $message);

            return true;
        }

        return false;
    }
}
