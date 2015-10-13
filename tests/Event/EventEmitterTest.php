<?php

use Fyuze\Event\Emitter;

class EventEmitterTest extends PHPUnit_Framework_TestCase
{
    public function testEventRegistersAndResolvesSingleEvent()
    {
        $emitter = new Emitter();
        $fired = false;
        $emitter->listen('foo', function () use (&$fired) {
            $fired = true;
            return 'bar';
        });
        $emitter->emit('foo');
        $this->assertTrue($fired);
    }

    public function testMultipleListenersBoundToSameEvent()
    {
        $emitter = new Emitter();
        $event1 = $event2 = false;
        $emitter->listen('foo', function () use (&$event1) {
            $event1 = true;
            return 'bar';
        });

        $emitter->listen('foo', function () use (&$event2) {
            $event2 = true;
            return 'baz';
        });

        $emitter->emit('foo');

        $this->assertTrue($event1);
        $this->assertTrue($event2);
    }

    public function testHasRecognizesRegisteredEvents()
    {
        $emitter = new Emitter();
        $emitter->listen('foo', function () {
            return 'bar';
        });
        $this->assertTrue($emitter->has('foo'));
        $this->assertFalse($emitter->has('bar'));
    }

    public function testDropReturnsCorrectValues()
    {
        $emitter = new Emitter();
        $emitter->listen('foo', function () {
            return 'bar';
        });

        $this->assertTrue($emitter->has('foo'));
        $this->assertTrue($emitter->drop('foo'));
        $this->assertFalse($emitter->has('foo'));
        $this->assertFalse($emitter->drop('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEmitterThrowsExceptionWithoutLogger()
    {
        (new Emitter())->emit('foo');
    }

    public function testEmitterLogsInsteadsOfDiesWithLogger()
    {
        /** @var \Fyuze\Log\Handlers\Log $mock */
        $logger = new \Fyuze\Log\Handlers\File([]);

        $emitter = new Emitter();
        $emitter->setLogger($logger);
        $emitter->emit('foo');

        $this->assertCount(1, $logger->getLogs());
        $this->assertEquals('Event foo has not been set', $logger->getLogs()[0]);
    }
}
