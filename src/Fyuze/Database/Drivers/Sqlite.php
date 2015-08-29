<?php
namespace Fyuze\Database\Drivers;

use PDO;

class Sqlite extends Driver
{
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @codeCoverageIgnore
     */
    public function open()
    {
        return new PDO(
            sprintf('sqlite:%s', $this->config['database']),
            null,
            null,
            $this->getOptions()
        );
    }
}
