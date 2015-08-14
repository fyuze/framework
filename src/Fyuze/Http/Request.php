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
     * @var array
     */
    protected $params = [];

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
     * @param array $attributes
     */
    public function __construct($uri, $method = 'GET', array $attributes = [])
    {
        $this->uri = $this->renderUri($uri);
        //$this->headers = $this->getHeaders();
        $this->ip = $this->getIp();
        $this->params = $attributes;

    }

    /**
     * @param string $uri
     * @param string $method
     * @param array $request
     * @return static
     */
    public static function create($uri = '', $method = 'GET', $request = [])
    {
        $server = array_replace(array(
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Fyuze/0.1.x',
            'REMOTE_ADDR' => '127.0.0.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_TIME' => time(),
            'REQUEST_URI' => $uri
        ), $_SERVER);

        return new static(
            $server['REQUEST_URI'],
            $method,
            empty($request) ? $_REQUEST : $request
        );
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $uri = $this->getUri();

        if (strpos($uri, '?')) {
            return explode('?', $uri)[0];
        }

        return $uri;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return array
     */
    public function setParams(array $params)
    {
        return $this->params = $params;
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
     * @param $uri
     * @return string
     */
    protected function renderUri($uri)
    {
        $uri = rtrim($uri, '/');

        if (mb_stripos($uri, '/index.php') === 0) {
            $uri = mb_substr($uri, 10);
        }

        return rawurldecode($uri);
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

            $filter = array_filter(['HTTP_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'REMOTE_ADDR'], function ($key) {
                return array_key_exists($key, $_SERVER);
            });

            $ip = count($filter) ? $_SERVER[reset($filter)] : null;
        }

        return (false !== filter_var($ip, FILTER_VALIDATE_IP)) ? $ip : '127.0.0.1';
    }
}
