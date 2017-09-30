<?php

use Fyuze\Debug\Collectors\Performance;
use PHPUnit\Framework\TestCase;

class PerformanceCollectorTest extends TestCase
{
    public function testCollectorGetsExecutionTime()
    {
        define('APP_START', microtime(true));
        $collector = new Performance();


        $this->assertArrayHasKey('title', $collector->tab());
    }
}
