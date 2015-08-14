<?php
namespace Fyuze\Kernel;

use Fyuze\Config\Config;
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
     */
    protected function init()
    {
        $this->setupContainer();
        $this->configure();

        $this->initialized = true;
    }

    /**
     *
     */
    protected function setupContainer()
    {
        $container = new Registry();
        $container->make($this);

        $this->config = new Config($this->getConfigPath(), 'prod');

        $container->make($this->config);
        $container->make(new Collection());

        $this->container = $container;
    }

    /**
     *
     */
    protected function configure()
    {
        $config = $this->config->get('app');

        $this->charset = $config['charset'];
        mb_internal_encoding($this->charset);

        date_default_timezone_set($config['timezone']);
    }

    /**
     * @return mixed
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
