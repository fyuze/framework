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
        $config = $this->registry->make('Fyuze\Config\Config')->get('app.error_handler');

        if ($config['log_errors'] === true) {

            $this->registry->make(
                new BaseLogger($config['log_prefix'])
            );
        }
    }
}
