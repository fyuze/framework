<?php

use PHPUnit\Framework\TestCase;

class DatabaseCollectorTest extends TestCase
{
    public function testCollectorGetsQueries()
    {
        $mock = Mockery::mock('Fyuze\Database\Db');
        $mock->shouldReceive('getQueries')->once()->andReturn([
            'title' => '1 Queries'
        ]);

        $collector = new \Fyuze\Debug\Collectors\Database($mock);

        $this->assertArrayHasKey('title', $collector->tab());
        $this->assertSame('1 Queries', $collector->tab()['title']);
    }
}
