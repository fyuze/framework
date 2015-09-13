<?php

namespace Fyuze\Log\Handlers;

use Psr\Log\AbstractLogger;

abstract class Log extends AbstractLogger implements Handler
{

    /**
     * @var array
     */
    protected $logs = [];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->logs[] = $message;

        $this->write($this->logs);
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
}
