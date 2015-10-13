<?php

use Fyuze\Log\Handlers\File;
use Fyuze\Log\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @expectedException RuntimeException
     */
    public function testLoggerThrowsExceptionWithoutHandler()
    {
        $logger = new Logger('foo');
        $logger->alert('hi, it\'s an alert');
    }
}
