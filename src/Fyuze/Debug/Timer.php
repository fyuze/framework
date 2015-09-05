<?php
namespace Fyuze\Debug;

use Debug;

class Timer
{
    /**
     * @var
     */
    protected $timers;

    /**
     * @param string $name
     * @return float
     */
    public function start($name = 'default')
    {
        $this->current = $name;

        $start = microtime(true);

        $this->timers[$name]['start'] = $start;

        return $start;
    }

    /**
     * @param null $name
     * @return array
     */
    public function stop($name = null)
    {
        $key = ($name !== null || ($name && array_key_exists($name, $this->timers))) ? $name : 'default';

        $this->timers[$key]['stop'] = microtime(true);

        /** @var $start float */
        /** @var $stop float */
        extract($this->timers[$key]);

        $this->timers[$key]['duration'] = $stop - $start;

        return $this->timers[$key];
    }

    /**
     *
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * @param \Closure $closure
     * @return array
     */
    public function transaction(\Closure $closure)
    {
        $this->start();

        $closure();

        return $this->stop();
    }
}
