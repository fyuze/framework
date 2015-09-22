<?php

use Fyuze\Debug\Collectors\Performance;

class PerformanceCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectorGetsExecutionTime()
    {
        define('APP_START', microtime(true));
        $collector = new Performance();


        $this->assertArrayHasKey('title', $collector->tab());
    }
}
