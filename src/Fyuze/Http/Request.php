<?php
namespace Fyuze\Http;

use RuntimeException;

class Request
{
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var
     */
    protected $ip;

    /**
     * Requested route
     *
     * @var string
     */
    protected $uri;

    /**
     * @var
     */
    protected $method;

    /**
     * @var
     */
    protected $route;

    /**
     * @param $uri
     * @param string $method
     * @param array $options
     */
    public function __construct($uri, $method = 'GET', array $options = [])
    {
        $this->uri = $uri;
        //$this->headers = $this->getHeaders();
        $this->ip = $this->getIp();

    }

    /**
     * @param string $uri
     * @param string $method
     * @return static
     */
    public static function create($uri = '', $method = 'GET')
    {
        return new static($uri, $method);
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get the users ip
     *
     * @return string
     */
    public function ip()
    {
        return $this->getIp();
    }

    /**
     * @param $key
     * @param null $value
     * @return null
     */
    public function header($key, $value = null)
    {
        if (null !== $value) {
            // We are setting a header
            return $this->headers[$key] = $value;
        }

        return array_key_exists($key, $this->headers) ? $this->headers[$key] : null;
    }

    /**
     * @param $key
     * @param null $value
     * @return null
     */
    public function server($key, $value = null)
    {
        if (null !== $value) {
            // We are setting a header
            return $_SERVER[$key] = $value;
        }

        return $_SERVER[$key];
    }
    /**
     * @return bool
     */
    public function isAjax()
    {
        return array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && $this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    /**
     * Internal method for getting users ip
     *
     * @return array|mixed
     * @throws \RuntimeException
     */
    protected function getIp()
    {
        if (!empty($this->header('HTTP_X_FORWARDED_FOR'))) {

            $ip = explode(',', $this->header('HTTP_X_FORWARDED_FOR'));
            $ip = array_pop($ip);
        }

        if (!isset($ip)) {
            foreach (['HTTP_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'REMOTE_ADDR'] as $key) {
                if (!empty($_SERVER[$key])) {
                    $ip = $_SERVER[$key];
                    break;
                }
            }
        }

        if (isset($ip) && false !== filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return '127.0.0.1';
    }
}
