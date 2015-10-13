<?php
namespace Fyuze\Log;

use RuntimeException;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Fyuze\Log\Handlers\Handler;

class Logger extends AbstractLogger implements LoggerInterface
{
    /**
     * Logging handlers
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * @var array
     */
    protected $logs = [];

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

            $this->logs[] = sprintf('[%s] %s', $level, $message);

            $handler->write($level, $message, $context);
        }

        return $this;
    }

    /**
     * Gets all logs as an array
     *
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
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
            $logs .= implode("\n", $this->getLogs());
        }

        return $logs;
    }
}
