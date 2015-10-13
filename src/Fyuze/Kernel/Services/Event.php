<?php
namespace Fyuze\Kernel\Services;

use Fyuze\Database\Db;
use Fyuze\Database\Drivers\Factory;
use Fyuze\Event\Emitter;
use Fyuze\Kernel\Service as BaseService;

class Event extends BaseService
{
    /**
     * Database services
     */
    public function services()
    {
        $this->registry->add('emitter', function ($app) {

            return (new Emitter())
                ->setLogger($app->make('logger'));
        });
    }
}
