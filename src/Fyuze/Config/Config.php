<?php
namespace Fyuze\Config;

use SplFileInfo;
use GlobIterator;
use InvalidArgumentException;
use Fyuze\Config\Parsers\PHP;
use Fyuze\Config\Parsers\Yaml;

class Config
{
    /**
     * The configuration path
     *
     * @var string
     */
    protected $path;

    /**
     * Application env
     *
     * @var string
     */
    protected $env;

    /**
     * Loaded configurations
     *
     * @var array
     */
    protected $configs;

    /**
     * @var array
     */
    protected $types = [
        Yaml::class,
        PHP::class
    ];

    /**
     * @param string $path
     * @param string $env
     */
    public function __construct($path, $env)
    {
        $this->path = $path;
        $this->env = $env;
        $this->load();

    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->configs)) {
            return $this->configs[$key];
        }

        $configs = $this->configs;

        foreach (explode('.', $key) as $value) {

            $configs = array_key_exists($value, $configs) ? $configs[$value] : $default;
        }

        return $configs;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        return $this->configs[$key] = $value;
    }

    /**
     *
     */
    protected function load()
    {
        if (!is_dir($this->path)) {
            throw new InvalidArgumentException(sprintf('The path you defined is not a valid directory: %s', $this->path));
        }

        foreach (new GlobIterator($this->path . '/*.*') as $file) {

            list($key, $value) = $this->getType($file);

            $this->configs[$key] = $value;
        }
    }

    /**
     * @param SplFileInfo $file
     * @return array
     */
    protected function getType(SplFileInfo $file)
    {
        $extension = $file->getExtension();
        $name = $file->getBasename(".$extension");

        $type = array_filter($this->types, function ($n) use ($extension) {
            return in_array($extension, $n::$extensions);
        });

        $class = reset($type);

        return [$name, (new $class)->parse($file)];
    }
}
