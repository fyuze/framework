<?php
namespace Fyuze\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{

    /**
     * @var
     */
    protected $serverParams;

    /**
     * @var
     */
    protected $cookieParams;

    /**
     * @var
     */
    protected $queryParams;

    /**
     * @var
     */
    protected $uploadedFiles;

    /**
     * @var
     */
    protected $parsedBody;

    /**
     * @var
     */
    protected $attributes = [];

    /**
     * ServerRequest constructor.
     * @param string $uri
     * @param string $method
     * @param array $server
     */
    public function __construct($uri = '/', $method = 'GET', $server = [])
    {
        $this->uri = ($uri instanceof UriInterface) ? $uri : new Uri($uri);
        $this->serverParams = $server;
    }

    /**
     * @param string $uri
     * @param string $method
     * @return static
     */
    public static function create($uri = '/', $method = 'GET')
    {
        $server = array_replace([
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Fyuze/0.1.x',
            'REMOTE_ADDR' => '127.0.0.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_TIME' => time(),
            'REQUEST_URI' => (string)$uri,
            'REQUEST_METHOD' => $method
        ], $_SERVER);


        /** @todo need a better way to handle this */
        if (mb_stripos($uri, '/index.php') === 0) {
            $uri = str_replace('/index.php', '', $uri);
        }

        return (new static(
            ($uri === '') ? '/' : $uri, $method, '', $server, [], []
        ))
            ->withMethod($method)
            ->withRequestTarget($uri)
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withUploadedFiles($_FILES);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * @return self
     */
    public function withCookieParams(array $cookies)
    {
        return $this->_clone('cookieParams', $cookies);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     * @return self
     */
    public function withQueryParams(array $query)
    {
        return $this->_clone('queryParams', $query);
    }

    /**
     * {@inheritdoc}
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     *
     * @param array An array tree of UploadedFileInterface instances.
     * @return self
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        return $this->_clone('uploadedFiles', $uploadedFiles);
    }

    /**
     * {@inheritdoc}
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return self
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data)
    {
        return $this->_clone('parsedBody', $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $default Default value to return if the attribute does not exist.
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (array_key_exists($name, $this->attributes) === false) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * {@inheritdoc}
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return self
     */
    public function withAttribute($name, $value)
    {
        $instance = clone($this);
        $instance->attributes[$name] = $value;
        return $instance;
    }

    /**
     * {@inheritdoc}
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @return self
     */
    public function withoutAttribute($name)
    {
        if (array_key_exists($name, $this->attributes) === false) {
            return clone $this;
        }

        $instance = clone $this;
        unset($instance->attributes[$name]);
        return $instance;
    }
}
