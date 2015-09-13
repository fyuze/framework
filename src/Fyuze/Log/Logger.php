<?php
namespace Fyuze\Log;

use RuntimeException;
use Fyuze\Log\Handlers\Handler;

class Logger
{
    /**
     * Logging handlers
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * Logger constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Register a logging handler
     *
     * @param Handler $handler
     */
    public function register(Handler $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return $this
     * @throws RuntimeException
     */
    public function log($level, $message, array $context = [])
    {
        if (count($this->handlers) === 0) {
            throw new RuntimeException('You must define at least one logging handler, none provided.');
        }

        foreach ($this->handlers as $handler) {
            $handler->$level($message, $context);
        }

        return $this;
    }

    /**
     * Turns logs into a string
     *
     * @return string
     */
    public function __toString()
    {
        $logs = '';

        /** @var \Fyuze\Log\Handlers\Log $handler */
        foreach ($this->handlers as $handler) {
            $logs .= implode("\n", $handler->getLogs());
        }

        return $logs;
    }

    /**
     * @param $method
     * @param $params
     *
     * @return Logger
     * @throws RuntimeException
     */
    public function __call($method, $params)
    {
        if (!array_key_exists(1, $params)) {
            $params[1] = [];
        }

        list($message, $context) = $params;

        return $this->log($method, $message, $context);
    }
}
