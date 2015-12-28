<?php
namespace Fyuze\Http;

use Fyuze\Http\Message\Response as PsrResponse;
use Fyuze\Http\Message\Stream;

class Response extends PsrResponse
{
    /**
     * @var string
     */
    protected $contentType = 'text/html';

    /**
     * @var boolean
     */
    protected $cache = false;

    /**
     * @var boolean
     */
    protected $compression = true;

    /**
     * @param $body
     * @param $code
     * @return Response
     */
    public static function create($body = '', $code = 200)
    {
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($body);
        return (new static)
            ->withStatus($code)
            ->withBody($stream);
    }

    /**
     * Set response caching, blank for default
     *
     * @param boolean $value
     * @return bool
     */
    public function setCache($value = false)
    {
        return $this->cache = $value;
    }

    /**
     * Set output compression, blank for default
     *
     * @param boolean $value
     * @return bool
     */
    public function setCompression($value = true)
    {
        return $this->compression = $value;
    }

    /**
     * Modify the response
     *
     * @param \Closure $closure
     */
    public function modify(\Closure $closure)
    {
        return $this->stream = $closure($this->stream);
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
        foreach ($this->headers as $key => $value) {
            header("$key: " . implode(',', $value));
        }

        if($this->hasHeader('Content-Type') === false) {
            header(vsprintf(
                'Content-Type: %s',
                $this->hasHeader('Content-Type') ? $this->getHeader('Content-Type') : [$this->contentType]
            ));
        }

        http_response_code($this->getStatusCode());

        if ($this->stream !== null) {
            if ($this->compression) {
                ob_start('ob_gzhandler');
            }
            echo (string)$this->getBody();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getBody();
    }
}
