<?php
namespace Fyuze\Config;

use Countable;
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
        $this->load($path, $env);
    }

    /**
     * @param $key
     * @param null $value
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($key, $value = null)
    {
        if (!array_key_exists($key, $this->configs) &&
            $value === null
        ) {
            throw new InvalidArgumentException('Invalid configuration being accessed without a value.');
        }

        if (null !== $value) {
            return $this->set($key, $value);
        }

        return $this->configs[$key];
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
     * @param $path
     * @param $env
     * @return array
     * @throws InvalidArgumentException
     */
    protected function load($path, $env)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException('The path you defined is not a valid directory: ' . $path);
        }

        foreach (new GlobIterator($path . '/*.*') as $file) {

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
