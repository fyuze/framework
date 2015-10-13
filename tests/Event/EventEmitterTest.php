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

    public function testListenerHasAppropriateArgs()
    {
        $emitter = new Emitter();
        $emitter->listen('foo', function ($obj) {
            $this->assertInstanceOf('StdClass', $obj);
            $this->assertEquals('bar', $obj->foo);
            return 'bar';
        });

        $stdClass = new StdClass;
        $stdClass->foo = 'bar';

        $emitter->emit('foo', $stdClass);
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

    public function testEmitterWithLoggerCapturesWhenFired()
    {
        $logger = new \Fyuze\Log\Handlers\File([]);

        $emitter = new Emitter();
        $emitter->setLogger($logger);
        $emitter->listen('foo', function () {
            return 'bar';
        });
        $emitter->listen('foo', function () {
            return 'bar';
        });
        $emitter->emit('foo');

        $this->assertCount(2, $logger->getLogs());
        $this->assertEquals('Event: foo fired: {}', $logger->getLogs()[0]);
    }

    public function testEmitterWithLoggerCapturesNoEventsMessage()
    {
        $logger = new \Fyuze\Log\Handlers\File([]);

        $emitter = new Emitter();
        $emitter->setLogger($logger);
        $emitter->emit('foo');

        $this->assertCount(1, $logger->getLogs());
        $this->assertEquals('Event foo called, but no listeners were registered', $logger->getLogs()[0]);
    }
}
