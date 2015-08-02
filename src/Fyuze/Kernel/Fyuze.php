<?php
namespace Fyuze\Kernel;

use Fyuze\Config\Config;
use Illuminate\Container\Container;

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
     * @var Container
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
        $container = new Container();

        $container->bind('config', function() {
            return new Config($this->getConfigPath(), 'prod');
        });

        $this->config = $container->make('config');

        $this->container = $container;
        $this->initialized = true;

        $config = $this->config->get('app');

        $this->charset = $config['charset'];
        mb_internal_encoding($this->charset);
    }

    abstract public function boot();
}
