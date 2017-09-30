<?php

use Fyuze\Database\Connection;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    public function testSqliteConnection()
    {
        $connection = new Connection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'fetch' => 'PDO::FETCH_OBJ',
            'charset' => 'UTF8'
        ]);

        $connection->first('SELECT 1');
    }
}
