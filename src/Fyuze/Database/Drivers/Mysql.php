<?php
namespace Fyuze\Database\Drivers;

use PDO;

class Mysql extends Driver
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @codeCoverageIgnore
     */
    public function open()
    {
        $dsn = $this->getDsn();

        return new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
    }

    /**
     * @return string
     */
    protected function getDsn()
    {
        return sprintf('mysql:host=%s;dbname=%s', $this->config['host'], $this->config['database']);
    }
}
