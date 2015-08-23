<?php
use Fyuze\Database\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testSqliteConnection()
    {
        $connection = new Connection([
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);


        $connection->query('');
    }
}