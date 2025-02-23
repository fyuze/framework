<?php

use Fyuze\Database\Drivers\Factory;
use PHPUnit\Framework\TestCase;

class DriverTest extends TestCase
{
    public function testCreatesMysqlDriver()
    {
        $driver = Factory::create([
            'driver' => 'mysql',
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'mysql',
            'database' => 'fyuze'
        ]);

        $this->assertInstanceOf('Fyuze\Database\Drivers\Mysql', $driver);
    }

    public function testCreatesSqliteDriver()
    {
        $driver = Factory::create([
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);

        $this->assertInstanceOf('Fyuze\Database\Drivers\Sqlite', $driver);
    }

    public function testThrowsExceptionForNonexistentDriver()
    {
        $this->expectException(\InvalidArgumentException::class);
        Factory::create(['driver' => 'foo']);
    }

    public function testThrowsExceptionWithoutDriverSpecified()
    {
        $this->expectException(\InvalidArgumentException::class);
        Factory::create([]);
    }
}
