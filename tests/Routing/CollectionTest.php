<?php

use Fyuze\Routing\Collection;
use PHPUnit\Framework\TestCase;

class RoutingCollectionTest extends TestCase
{
    public function testCollectionAddsRoutes()
    {
        $collection = new Collection();
        $collection->get('/', 'index', function () {
            return 'Hello, World!';
        });

        $this->assertCount(1, $collection->getRoutes());

        $collection->post('/hello-again', 'hello', function () {
            return 'Hello again, world!';
        });

        $this->assertCount(2, $collection->getRoutes());
    }
}
