<?php
namespace Fyuze\Debug;

use Fyuze\Debug\Collectors\Collector;
use Fyuze\View\View;

class Toolbar
{
    /**
     * @var array
     */
    protected $collectors = [];

    /**
     * @param Collector $collector
     * @param bool|false $first
     */
    public function addCollector(Collector $collector, $first = false)
    {
        if ($first === true) {

            array_unshift($this->collectors, $collector);
        }
        else {

            $this->collectors[] = $collector;
        }

    }

    /**
     * @return View
     */
    public function render()
    {
        return new View(__DIR__ . '/Resources/views/toolbar.php', [
            'collectors' => $this->collectors
        ]);
    }
}
