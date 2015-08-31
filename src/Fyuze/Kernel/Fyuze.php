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
        $this->registerServices();
        $this->errorHandling();

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
        $container->make($this);

        $this->config = new Config($this->getConfigPath(), 'prod');

        $container->make($this->config);
        $container->make(new Collection());

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

        $this->registerServices();
    }

    /**
     * Registers all defined services
     *
     * @return void
     */
    protected function registerServices()
    {
        foreach ($this->config->get('app.services') as $service) {
            /** @var \Fyuze\Kernel\Service $obj */
            $obj = new $service($this->container);
            $obj->services();
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

        $this->container->make($handler);

        set_error_handler(function ($severity, $message, $file, $line) {
            if (error_reporting() && $severity) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }

            return true;
        });
    }

    /**
     * @return Collection
     */
    protected function loadRoutes()
    {
        $router = $this->container->make('Fyuze\Routing\Collection');
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
