<?php
namespace Fyuze\Database\Drivers;

use PDO;

abstract class Driver implements ConnectionInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            PDO::ATTR_PERSISTENT => array_key_exists('persistent', $this->config) ? $this->config['persistent'] : false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => constant($this->config['fetch'])
        ];
    }
}
