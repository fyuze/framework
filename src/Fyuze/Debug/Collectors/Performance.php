<?php
namespace Fyuze\Debug\Collectors;

class Performance implements Collector
{
    /**
     * @return string
     */
    public function tab()
    {
        return ['title' => sprintf('%Gms', round(microtime(true) - APP_START, 6))];
    }
}
