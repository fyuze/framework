<?php
namespace Fyuze\Kernel\Services;

use Fyuze\Log\Logger as BaseLogger;
use Fyuze\Kernel\Service as BaseService;

class Logger extends BaseService
{
    /**
     *
     */
    public function services()
    {
        $config = $this->registry->make('config')->get('app.error_handler');

        if ($config['log_errors'] === true) {

            $this->registry->add('logger', function () use ($config) {
                return new BaseLogger($config['log_prefix']);
            });
        }
    }
}
