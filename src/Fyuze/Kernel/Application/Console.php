<?php
namespace Fyuze\Kernel\Application;

use Fyuze\Cmd\Shell;
use Fyuze\Kernel\Fyuze;

class Console extends Fyuze
{

    public function boot()
    {
        $cli = new Shell($this->registry);

        return $cli->run();
    }
}
