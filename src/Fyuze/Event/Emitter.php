<?php
namespace Fyuze\Event;

use Psr\Log\LoggerInterface;
use InvalidArgumentException;

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
    public function emit($name, $params = null)
    {
        foreach ($this->locate($name) as $event) {

            call_user_func_array(
                $event,
                is_array($params) ? $params : [$params]
            );

            $this->log(sprintf('Event: %s fired: %s', $name, json_encode($event)));
        }
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
     * @param $name
     * @return array
     */
    protected function locate($name)
    {
        if (!$this->has($name)) {

            $this->log(sprintf('Event %s called, but no listeners were registered', $name));

            return [];
        }

        return $this->events[$name];

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
