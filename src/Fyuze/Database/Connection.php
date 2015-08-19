<?php
namespace Fyuze\Database;

use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @param string $name
     * @param array $info
     */
    public function __construct(array $info = [])
    {
        try {
            list($config, $options) = $this->parseInfo($info);

            $this->pdo = new PDO($config['dsn'], $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new RuntimeException(sprintf("Failed to connect to the database. %s", $e->getMessage()));
        }
    }

    /**
     * @return mixed
     */
    public function getDriver()
    {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * @return PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * @param array $info
     * @return array
     */
    protected function parseInfo(array $info = [])
    {
        return [
            $this->buildConfig($info),
            $this->buildOptions($info)
        ];
    }

    /**
     * @param $info
     * @return array
     */
    protected function buildConfig($config)
    {
        $config = array_replace([
            'driver' => null,
            'username' => null,
            'password' => null
        ], $config);

        switch ($config['driver']) {
            case 'mysql':
                $config['dsn'] = "mysql:dbname={$config['database']};host={$config['host']}";
                break;
            case 'sqlite':
                $config['dsn'] = "sqlite:{$config['database']}";
                break;
            default:
                $config['dsn'] = null;
                break;
        }

        return $config;
    }

    /**
     * @param $info
     * @return array
     */
    protected function buildOptions($info)
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => array_key_exists('persistent', $info) ? $info['persistent'] : false,
            PDO::ATTR_DEFAULT_FETCH_MODE => isset($info['fetch_mode']) ? $info['fetch_mode'] : PDO::FETCH_OBJ,
        ];
    }
}
