<?php
namespace Fyuze\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    /**
     * @var
     */
    protected $method;

    /**
     * @var string
     */
    protected $requestTarget;

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        if (!$this->uri) {
            return '/';
        }

        $requestTarget = $this->getUri()->getPath();
        if ($query = $this->getUri()->getQuery()) {
            $requestTarget .= sprintf('?%s', $query);
        }

        return empty($requestTarget) ? '/' : $requestTarget;
    }

    /**
     * {@inheritdoc}
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        return $this->_clone('requestTarget', $requestTarget);
    }

    /**
     * {@inheritdoc}
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $method Case-sensitive method.
     * @return self
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $method = strtoupper($method);

        if (false === in_array($method, ['GET', 'HEAD', 'POST', 'PUT', 'OPTIONS', 'DELETE'])) {
            throw new \InvalidArgumentException(
                sprintf('Invalid HTTP method: %s', $method)
            );
        }

        return $this->_clone('method', $method);
    }

    /**
     * {@inheritdoc}
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = $this->_clone('uri', $uri);

        if ($preserveHost === true || $uri->getHost() === null) {

            return $clone;
        }

        $clone->headers['host'] = $uri->getHost();

        return $clone;

    }
}
