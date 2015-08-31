<?php
namespace Fyuze\Database;

use Exception;
use Fyuze\Database\Drivers\ConnectionInterface;

class Db
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection->open();
    }

    /**
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function all($sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function first($sql, array $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * @param $name
     * @return Query
     */
    public function table($name)
    {
        return new Query($this, $name);
    }

    /**
     * @param \Closure $queries
     * @return bool
     */
    public function transaction(\Closure $queries)
    {
        $this->connection->beginTransaction();

        try {

            $queries($this);

            $this->connection->commit();

            return true;

        } catch (Exception $e) {

            $this->connection->rollback();

            return false;
        }
    }

    /**
     * @param $query
     * @param array $params
     * @return mixed
     */
    public function query($query, array $params = [])
    {
        $statement = $this->connection->prepare($query);

        $result = $statement->execute($params);

        if ($this->isRead($query)) {

            return $statement;

        } elseif ($this->isWrite($query)) {

            return $statement->rowCount();
        }

        return $result;
    }

    /**
     *
     * @param $query
     * @return bool
     */
    protected function isRead($query)
    {
        return stripos($query, 'select') === 0;
    }

    /**
     *
     * @param $query
     * @return bool
     */
    protected function isWrite($query)
    {
        return stripos($query, 'update') === 0 || stripos($query, 'delete') === 0;
    }
}
