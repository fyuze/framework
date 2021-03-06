<?php
namespace Fyuze\Kernel;

use Fyuze\Config\Config;
use Fyuze\Error\ErrorHandler;
use Fyuze\Routing\Collection;

abstract class Fyuze
{

    /**
     * The framework version
     *
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * has the application initalized?
     *
     * @var boolean
     */
    protected $initialized = false;

    /**
     * the IoC container
     *
     * @var Registry
     */
    protected $container;

    /**
     * Application path
     *
     * @var string
     */
    protected $path;

    /**
     * Configuration path
     *
     * @var
     */
    protected $configPath;

    /**
     * Config
     *
     * @var
     */
    protected $config;

    /**
     * Default Charset
     *
     * @var
     */
    protected $charset;

    /**
     * User Locale
     *
     * @var
     */
    protected $locale;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * Initialize application
     *
     * @param string $path
     */
    public function __construct($path = '')
    {
        $this->path = $path;
        $this->init();
    }

    /**
     * @return Registry
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Initialize services
     *
     * return @void
     */
    protected function init()
    {
        $this->setupContainer();
        $this->configure();
        $this->errorHandling();
        $this->registerServices();

        $this->initialized = true;
    }

    /**
     * Registers container and some classes
     *
     * @return void
     */
    protected function setupContainer()
    {
        $container = Registry::init();
        $container->add('app', $this);
        $container->add('registry', $container);

        $container->add('config', function () {
            return new Config($this->getConfigPath(), 'prod');
        });

        $this->config = $container->make('config');

        $container->add('routes', function () {
            return new Collection();
        });

        $this->container = $container;
    }

    /**
     * Sets up basic application configurations
     *
     * @return void
     */
    protected function configure()
    {
        $config = $this->config->get('app');

        $this->charset = $config['charset'];
        mb_internal_encoding($this->charset);

        date_default_timezone_set($config['timezone']);
    }

    /**
     * Registers all defined services
     *
     * @return void
     */
    protected function registerServices()
    {
        $services = array_filter($this->config->get('app.services'), function ($service) {
            return class_exists($service);
        });

        foreach ($services as $service) {
            /** @var \Fyuze\Kernel\Service $obj */
            $obj = new $service($this->container);
            $obj->services();

            $this->services[] = $obj;
        }
    }

    /**
     * Registers error handler with container
     * And converts all errors into ErrorExceptions
     *
     * @return void
     */
    protected function errorHandling()
    {
        $handler = new ErrorHandler();

        $this->container->add('error', $handler);
    }

    /**
     * @return Collection
     */
    protected function loadRoutes()
    {
        $router = $this->container->make('routes');
        $routes = realpath($this->getPath() . '/app/routes.php');

        if (file_exists($routes)) {
            require $routes;
        }

        return $router;
    }

    /**
     * @return mixed
     */
    abstract public function boot();
}
