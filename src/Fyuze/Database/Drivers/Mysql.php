<?php
namespace Fyuze\Database\Drivers;

use PDO;

class Mysql extends Driver
{
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
        return new PDO($this->getDsn(), $this->config['username'], $this->config['password'], $this->getOptions());
    }

    /**
     * @return string
     */
    protected function getDsn()
    {
        return sprintf('mysql:host=%s;dbname=%s', $this->config['host'], $this->config['database']);
    }
}
