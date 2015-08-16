<?php
namespace Fyuze\Http;

use RuntimeException;

class Request
{
    /**
     * Server vars $_SERVER
     *
     * @var
     */
    protected $server;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Query string paramaters $_GET
     *
     * @var
     */
    protected $params;

    /**
     * User input $_POST
     *
     * @var
     */
    protected $input;

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
     * @var string
     */
    protected $path;

    /**
     * @var
     */
    protected $method;

    /**
     * @param $server
     * @param $params
     * @param $input
     */
    public function __construct($server, $params, $input)
    {
        $this->bootstrap($server, $params, $input);
    }

    /**
     * @param string $uri
     * @param string $method
     * @return static
     */
    public static function create($uri = '/', $method = 'GET')
    {
        $server = array_replace(array(
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Fyuze/0.1.x',
            'REMOTE_ADDR' => '127.0.0.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_TIME' => time(),
            'REQUEST_URI' => $uri,
            'REQUEST_METHOD' => $method
        ), $_SERVER);

        return new static(
            $server,
            $_GET,
            $_POST
        );
    }

    /**
     * @param $server
     * @param $params
     * @param $input
     */
    protected function bootstrap($server, $params, $input)
    {
        $this->server = $server;
        $this->params = $params;
        $this->input = $input;
        $this->headers = $this->getHeaders();

        $this->uri = $this->renderUri($server['REQUEST_URI']);
        $this->method = $server['REQUEST_METHOD'];
    }

    /**
     * @param $key
     * @param null $value
     * @return null
     */
    public function server($key, $value = null)
    {
        if (null !== $value) {
            return $this->server[$key] = $value;
        }

        return $this->server[$key];
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        if (count($this->headers)) {
            return $this->headers;
        }

        return $this->headers;
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

        if (mb_stripos($uri, '/index.php') === 0) {
            $uri = str_replace('/index.php', '', $uri);
        }

        if (strpos($uri, '?') !== false) {
            return explode('?', $uri)[0];
        }

        return ($uri === '') ? '/' : $uri;
    }

    /**
     * @return mixed
     */
    public function input()
    {
        return $this->input;
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
            return $this->headers[$key] = $value;
        }

        return array_key_exists($key, $this->headers) ? $this->headers[$key] : null;
    }


    /**
     * @return bool
     */
    public function isAjax()
    {
        return array_key_exists('HTTP_X_REQUESTED_WITH',
            $this->server) && $this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    /**
     * @param $uri
     * @return string
     */
    protected function renderUri($uri)
    {
        $uri = rtrim($uri, '/');

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
                return array_key_exists($key, $this->server);
            });

            $ip = count($filter) ? $this->server[reset($filter)] : null;
        }

        return (false !== filter_var($ip, FILTER_VALIDATE_IP)) ? $ip : '127.0.0.1';
    }
}
