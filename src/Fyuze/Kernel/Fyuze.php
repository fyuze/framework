<?php
namespace Fyuze\Kernel;

use Fyuze\Config\Config;
use Symfony\Component\Yaml\Exception\RuntimeException;

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
     * The registry container
     *
     * @var Registry
     */
    protected $registry;

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
    public function getRegistry()
    {
        return $this->registry;
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
        return $this->getPath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Initialize services
     */
    protected function init()
    {
        $registry = Registry::init();
        $registry->make($this);

        $this->config = new Config($this->getConfigPath(), 'prod');
        $registry->make($this->config);

        $this->registry = $registry;
        $this->initialized = true;

        $config = $this->config->get('app');

        $this->charset = $config['charset'];
        mb_internal_encoding($this->charset);
    }

    abstract public function boot();
}
