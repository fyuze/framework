<?php

use Fyuze\Log\Handlers\File;
use Fyuze\Log\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testLoggerWithHandler()
    {
        $logger = new Logger('foo');
        $logger->register(
            new File('my.log')
        );
        $logger->log('alert', 'hi, it\'s an alert');

        $this->assertSame("[alert] hi, it's an alert", (string)$logger);
    }

    public function testLoggerThrowsExceptionWithoutHandler()
    {
        $this->expectException(RuntimeException::class);
        $logger = new Logger('foo');
        $logger->alert('hi, it\'s an alert');
    }
}
