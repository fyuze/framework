<?php
namespace Fyuze\Http;

class Response
{
    /**
     * @var string
     */
    protected $contentType = 'text/html';

    /**
     * Response headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Response code
     *
     * @var int
     */
    protected $code;

    /**
     * Response body
     *
     * @var string
     */
    protected $body;

    /**
     * @var boolean
     */
    protected $cache = false;

    /**
     * @var boolean
     */
    protected $compression = true;

    /**
     * @param null $body
     * @param int $code
     */
    public function __construct($body = null, $code = 200)
    {
        $this->body = $body;
        $this->code = $code;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function header($key, $value = null)
    {
        if (null !== $value) {
            // We are setting a header
            return $this->headers[$key] = $value;
        }

        return $this->headers[$key];
    }

    /**
     * Set response caching, blank for default
     *
     * @param boolean $value
     */
    public function setCache($value = false)
    {
        $this->cache = $value;
    }

    /**
     * Set output compression, blank for default
     *
     * @param boolean $value
     */
    public function setCompression($value = true)
    {
        $this->compression = $value;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * Get response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sends the response
     * We ignore coverage here because
     * headers cannot be tested in cli sapi
     *
     * @codeCoverageIgnore
     */
    public function send()
    {
        header('Content-Type: ' . $this->contentType);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        if ($this->body !== null) {
            if ($this->compression) {
                ob_start('ob_gzhandler');
            }
            echo $this->body;
        }
    }
}
