<?php

use Fyuze\Database\Drivers\Factory;
use PHPUnit\Framework\TestCase;

class DatabaseFactoryTest extends TestCase
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionForNonexistentDriver()
    {
        Factory::create(['driver' => 'foo']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWithoutDriverSpecified()
    {
        Factory::create([]);
    }
}
