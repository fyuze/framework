<?php
namespace Fyuze\Database\Drivers;

use InvalidArgumentException;

class Factory
{
    /**
     * @param array $config
     * @return Mysql|Sqlite
     * @throws InvalidArgumentException
     */
    public static function create($config)
    {
        if(!array_key_exists('driver', $config)) {
            throw new InvalidArgumentException('You must specify a driver');
        }

        switch ($config['driver']) {
            case 'mysql':
                return new Mysql($config);

            case 'sqlite':
                return new Sqlite($config);
            
            default:
                throw new InvalidArgumentException('Invalid or no driver specified');
        }
    }
}
