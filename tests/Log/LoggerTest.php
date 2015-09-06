<?php
namespace Log;

use Fyuze\Log\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testPlainLogger()
    {
        $logger = new Logger('test');
        $logger->alert('hi, it\'s an alert');

        $this->assertSame("hi, it's an alert\n", (string)$logger);
    }

    public function testEmailLogger()
    {
        //$logger = new EmailLogger('matthew.javelet@gmail.com');

    }
}
