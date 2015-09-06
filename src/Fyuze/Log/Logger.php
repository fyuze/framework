<?php
namespace Fyuze\Log;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    /**
     * Logger constructor.
     * @param $name
     * @param array $options
     */
    public function __construct($name, $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

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
     * @return string
     */
    public function __toString()
    {
        $logs = '';
        foreach ($this->getLogs() as $log) {
            $logs .= "$log\n";
        }

        return $logs;
    }
}
