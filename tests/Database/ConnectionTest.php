<?php
use Fyuze\Database\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testWorkingConnection()
    {
        $connection = new Connection(['driver' => 'sqlite', 'database' => ':memory:']);
        $this->assertInstanceOf('PDO', $connection->getPDO());
        $this->assertEquals('sqlite', $connection->getDriver());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFailingConnection()
    {
        new Connection();
    }
}
