<?php
namespace Fyuze\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    /**
     *
     * @var string
     */
    protected $protocol;

    /**
     *
     * @var array
     */
    protected $headers = [];

    /**
     *
     * @var StreamInterface
     */
    protected $stream;

    /**
     * {@inheritdoc}
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $version HTTP protocol version
     * @return self
     * @throws InvalidArgumentException
     */
    public function withProtocolVersion($version)
    {
        if (in_array($version, ['1.0', '1.1', '2.0']) === false) {
            throw new InvalidArgumentException('You may only use a valid http protocol version, %d provided', $version);
        }

        return $this->_clone('protocol', $version);
    }

    /**
     * {inheritdoc}
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders()
    {
        $headers = [];

        foreach($this->headers as $name => $values) {
            $headers[strtolower($name)] = $values;
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return array_key_exists(
            strtolower($name),
            $this->getHeaders()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name)
    {
        $name = strtolower($name);

        return $this->hasHeader($name) ? $this->headers[$name] : [];
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        if (array_key_exists($name, $this->headers) && in_array($value, $this->headers[$name])) {
            return $this;
        }

        $instance = clone $this;
        $instance->headers[$name] = array_filter((array)$value);
        return $instance;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        $instance = clone $this;
        $instance->headers[$name][] = $value;
        return $instance;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name)
    {
        $request = new static($this);
        unset($request->headers[$name]);
        return $request;
    }

    /**
     * {@inheritdoc}
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->stream;
    }

    /**
     * {@inheritdoc}
     *
     * @param StreamInterface $body Body.
     * @return self
     * @throws InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        return $this->_clone('stream', $body);
    }

    /**
     * @param $key
     * @param $value
     * @return static
     */
    protected function _clone($key, $value)
    {
        if ($this->$key === $value) {
            return $this;
        }

        $instance = clone $this;
        $instance->$key = $value;
        return $instance;
    }
}
