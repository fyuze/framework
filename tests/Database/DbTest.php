<?php
use Fyuze\Database\Db;

class DatabaseDbTest extends \PHPUnit_Framework_TestCase
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

        $conn = Mockery::mock('\Fyuze\Database\Connection');
        $conn->shouldReceive('getPDO')->once()->andReturn($pdo);

        $db = new Db($conn);

        $first = $db->query('SELECT * FROM users WHERE name = ?', ['matthew'])->first();
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

        $conn = Mockery::mock('\Fyuze\Database\Connection');
        $conn->shouldReceive('getPDO')->once()->andReturn($pdo);

        $db = new DB($conn);

        $query = $db->query('SELECT * FROM users WHERE name = ?', ['matthew'])->all();
        $this->assertEquals(1, count($query));
    }
}
