<?php

use Fyuze\Database\Db;
use PHPUnit\Framework\TestCase;

class DatabaseDbTest extends TestCase
{
    public function testQueryFindsFirst()
    {
        $user = new StdClass;
        $user->name = 'matthew';

        $statement = Mockery::mock('\PDOStatement');
        $statement->shouldReceive('execute')->once()
            ->with(['matthew'])
            ->andReturn(true);
        $statement->shouldReceive('fetch')->once()
            ->andReturn($user);

        $pdo = Mockery::mock('\PDO');
        $pdo->shouldReceive('prepare')->once()
            ->with('SELECT * FROM users WHERE name = ?')
            ->andReturn($statement);

        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturn($pdo);

        $db = new Db($conn);

        $first = $db->first('SELECT * FROM users WHERE name = ?', ['matthew']);
        $this->assertEquals('matthew', $first->name);
    }

    public function testQueryFindsAll()
    {
        $user = new StdClass;
        $user->name = 'matthew';

        $statement = Mockery::mock('\PDOStatement');
        $statement->shouldReceive('execute')->once()
            ->with(['matthew'])
            ->andReturn(true);
        $statement->shouldReceive('fetchAll')->once()
            ->andReturn([$user]);

        $pdo = Mockery::mock('\PDO');
        $pdo->shouldReceive('prepare')->once()
            ->with('SELECT * FROM users WHERE name = ?')
            ->andReturn($statement);

        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturn($pdo);

        $db = new DB($conn);

        $query = $db->all('SELECT * FROM users WHERE name = ?', ['matthew']);
        $this->assertEquals(1, count($query));
    }

    public function testWriteQuery()
    {
        $user = new StdClass;
        $user->name = 'matthew';

        $statement = Mockery::mock('\PDOStatement');
        $statement->shouldReceive('execute')->once()
            ->with(['bob', 'matthew'])
            ->andReturn(true);
        $statement->shouldReceive('rowCount')->once()
            ->andReturn(1);

        $pdo = Mockery::mock('\PDO');
        $pdo->shouldReceive('prepare')->once()
            ->with('UPDATE users SET name = ? WHERE name = ?')
            ->andReturn($statement);

        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturn($pdo);

        $db = new Db($conn);

        $query = $db->query('UPDATE users SET name = ? WHERE name = ?', ['bob', 'matthew']);
        $this->assertEquals(1, $query);
    }

    public function testSuccessfulDbTransaction()
    {
        $pdo = Mockery::mock('\PDO');
        $pdo->shouldReceive('beginTransaction')->once()->andReturnNull();
        $pdo->shouldReceive('prepare')->times(3)->andReturnSelf();
        $pdo->shouldReceive('execute')->times(3)->andReturn(true);
        $pdo->shouldReceive('commit')->once()->andReturn(true);

        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturn($pdo);

        $db = new Db($conn);

        $result = $db->transaction(function ($query) {
            $query->query('INSERT INTO users (name) VALUES (matthew)');
            $query->query('INSERT INTO users (name) VALUES (bob)');
            $query->query('INSERT INTO users (name) VALUES (steve)');
        });

        $this->assertTrue($result);
    }

    public function testFailingDbTransaction()
    {
        $statement = Mockery::mock('\PDOStatement');
        $statement->shouldReceive('execute')->once()->andThrow(new \Exception('woopsies'));

        $pdo = Mockery::mock('\PDO');
        $pdo->shouldReceive('beginTransaction')->once()->andReturnNull();
        $pdo->shouldReceive('prepare')->times(3)->andReturn($statement);
        $pdo->shouldReceive('execute')->times(3)->andReturn();
        $pdo->shouldReceive('commit')->once()->andReturn();
        $pdo->shouldReceive('rollBack')->once()->andReturn();

        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturn($pdo);

        $db = new Db($conn);

        $result = $db->transaction(function ($query) {
            $query->first('SELECT 1');
        });

        $this->assertFalse($result);
    }

    public function testDatabaseResolvesBuilder()
    {
        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturnSelf();

        $db = new Db($conn);

        $this->assertInstanceOf('Fyuze\Database\Query', $db->table('users'));
    }

    public function testNonReadOrWriteReturnsStatement()
    {
        $statement = Mockery::mock('\PDOStatement');
        $statement->shouldReceive('execute')->once()
            ->andReturnSelf();

        $pdo = Mockery::mock('\PDO');
        $pdo->shouldReceive('prepare')->once()
            ->with('SET NAMES UTF-8')
            ->andReturn($statement);

        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturn($pdo);

        $db = new Db($conn);

        $query = $db->query('SET NAMES UTF-8');
        $this->assertInstanceOf('\PDOSTatement', $query);
    }

    public function testCountsCorrectNumberOfExecutedQueries()
    {
        $statement = Mockery::mock('\PDOStatement');
        $statement->shouldReceive('execute')->once()
            ->andReturnSelf();

        $pdo = Mockery::mock('\PDO');
        $pdo->shouldReceive('prepare')->twice()
            ->with('SELECT 1')
            ->andReturn($statement);

        $conn = Mockery::mock('\Fyuze\Database\Drivers\ConnectionInterface');
        $conn->shouldReceive('open')->once()->andReturn($pdo);

        $db = new Db($conn);

        $query = $db->query('SELECT 1');
        $query = $db->query('SELECT 1');
        $this->assertCount(2, $db->getQueries());
    }
}
