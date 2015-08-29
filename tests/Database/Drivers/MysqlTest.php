<?php

use Fyuze\Database\Drivers\Mysql;

class MysqlTest extends \PHPUnit_Framework_TestCase
{
    public function testDriverResolvesDsn()
    {
        $driver = new Mysql([
            'driver' => 'mysql',
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'mysql',
            'database' => 'fyuze'
        ]);

        $reflection = new ReflectionClass($driver);
        $method = $reflection->getMethod('getDsn');
        $method->setAccessible(true);

        $dsn = $method->invoke($driver);

        $this->assertEquals('mysql:host=localhost;dbname=fyuze', $dsn);
    }
}
