<?php
namespace Fyuze\Kernel\Services;

use Fyuze\Database\Db;
use Fyuze\Database\Drivers\Factory;
use Fyuze\Kernel\Service as BaseService;

class Database extends BaseService
{
    /**
     * Database services
     */
    public function services()
    {
        $config = $this->registry->make('Fyuze\Config\Config')->get('database');

        $default = $config['default'];

        $this->registry->make(
            new Db(Factory::create($config['connections'][$default]))
        );
    }
}
