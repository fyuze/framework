<?php
namespace Fyuze\Database;


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
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $query
     * @param array $params
     * @return mixed
     */
    public function query($query, array $params = [])
    {
        $statement = $this->connection->getPDO()->prepare($query);

        $result = $statement->execute($params);

        $this->query = $statement;

        if (stripos($query, 'select') !== false) {

            return $this;
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
}
