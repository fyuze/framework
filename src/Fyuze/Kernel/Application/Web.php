<?php
namespace Fyuze\Kernel\Application;

use Fyuze\Http\Response;
use Fyuze\Kernel\Fyuze;

class Web extends Fyuze
{
    /**
     * @return mixed
     */
    public function boot()
    {
        return (new Response($this->registry->make('Fyuze\Http\Request')))->send();
    }
}
