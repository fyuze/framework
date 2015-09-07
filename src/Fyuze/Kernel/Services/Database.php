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
        $this->registry->add('db', function () {

            $config = $this->registry->make('config')->get('database');

            $default = $config['connections'][$config['default']];

            return new Db(Factory::create(array_merge($default, ['fetch' => $config['fetch']])));
        });
    }
}
