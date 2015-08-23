<?php
namespace Fyuze\Database;

use Fyuze\Database\Drivers\Factory;
use Fyuze\Database\Drivers\ConnectionInterface;

class Connection
{
    /**
     * @var Db
     */
    protected $database;

    /**
     * @var bool
     */
    protected $connected = false;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->initialize($config);
    }

    /**
     * @param $config
     */
    protected function initialize($config)
    {
        /** @var ConnectionInterface $factory */
        $driver = Factory::create($config);

        $this->database = new Db($driver);
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array([$this->database, $method], $params);
    }
}
