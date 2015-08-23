<?php
namespace Fyuze\Database;

use Fyuze\Database\Drivers\ConnectionInterface;

class Db
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Current query object
     *
     * @var \PDOStatement
     */
    protected $query;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection->open();
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

        $this->query = $statement;

        if ($this->isRead($query)) {

            return $this;

        } elseif ($this->isWrite($query)) {

            return $statement->rowCount();
        }

        return $result;
    }

    /**
     *
     * @return array
     */
    public function all()
    {
        return $this->query->fetchAll();
    }

    /**
     *
     * @return mixed
     */
    public function first()
    {
        return $this->query->fetch();
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
