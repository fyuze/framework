<?php
namespace Fyuze\Kernel\Services;

use Fyuze\Debug\Collectors\Performance;
use Fyuze\Debug\Collectors\Response;
use Fyuze\Debug\Toolbar;
use Fyuze\Kernel\Service;
use Fyuze\Debug\Collectors\Database as DatabaseCollector;

class Debug extends Service
{

    /**
     * Once the has started
     */
    public function bootstrap()
    {
        $this->registry->make('toolbar')
            ->addCollector(new Response($this->registry->make('response')), true);
    }

    /**
     * @return mixed
     */
    public function services()
    {
        $this->registry->add('toolbar', function ($app) {

            $toolbar = new Toolbar();
            $collectors = [];

            //$collectors[] = new Response($this->registry->make('response'));
            $collectors[] = new Performance();
            $collectors[] = new DatabaseCollector($this->registry->make('db'));

            foreach ($collectors as $collector) {

                $toolbar->addCollector($collector);
            }

            return $toolbar;
        });
    }
}
