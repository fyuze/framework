<?php
namespace Fyuze\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class Uri implements UriInterface
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $scheme = '';

    /**
     * @var string
     */
    protected $authority;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $pass;

    /**
     * @var string
     */
    protected $userInfo;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var string
     */
    protected $fragment;

    /**
     * @var array
     */
    protected $schemes = [
        'http' => 80,
        'https' => 443,
    ];

    /**
     * @param $uri
     */
    public function __construct($uri = '')
    {
        if ($uri !== '') {
            $this->format($uri);
        }

        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();

        return rtrim(($userInfo ? $userInfo . '@' : '')
            . $this->host
            . (!$this->isStandardPort($this->scheme, $this->port) ? ':' . $this->port : ''),
            ':');
    }

    /**
     * {@inheritdoc}
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * {@inheritdoc}
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->isStandardPort($this->scheme, $this->port)
            ? null : $this->port;
    }

    /**
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return self A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $scheme = str_replace('://', '', strtolower((string)$scheme));

        if (!empty($scheme) && !array_key_exists($scheme, $this->schemes)) {
            throw new InvalidArgumentException('Invalid scheme provided.');
        }

        return $this->_clone('scheme', $scheme);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $userInfo = implode(':', [$user, $password]);

        return $this->_clone('userInfo', rtrim($userInfo, ':'));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $host
     * @return Uri
     */
    public function withHost($host)
    {
        return $this->_clone('host', $host);
    }

    /**
     * {@inheritdoc}
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return self A new instance with the specified port.
     * @throws InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        if ($port === null) {
            return $this->_clone('port', null);
        }

        if ($port < 1 || $port > 65535) {
            throw new InvalidArgumentException('Invalid port specified');
        }

        return $this->_clone('port', (int)$port);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $path The path to use with the new instance.
     * @return self A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        if (!is_string($path) || strpos($path, '#') !== false || strpos($path, '?') !== false) {
            throw new \InvalidArgumentException('Path must be a string and cannot contain a fragment or query string');
        }

        if (!empty($path) && '/' !== substr($path, 0, 1)) {
            $path = '/' . $path;
        }

        return $this->_clone('path', $this->filterPath($path));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $query The query string to use with the new instance.
     * @return self A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        return $this->_clone('query', $this->filter($query));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return self A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        if (strpos($fragment, '#') === 0) {
            $fragment = substr($fragment, 1);
        }

        return $this->_clone('fragment', $this->filter($fragment));
    }

    /**
     * {@inheritdoc}
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return ($scheme ? $scheme . '://' : '')
        . ($authority ?: '')
        . $path
        . ($query ? '?' . $query : '')
        . ($fragment ? '#' . $fragment : '');
    }

    /**
     * @param $uri
     */
    protected function format($uri)
    {
        $parsed = parse_url($uri);

        if (false === $parsed) {
            throw new RuntimeException('Seriously malformed url');
        }

        foreach (parse_url($uri) as $key => $val) {
            $this->$key = $val;
        }

        $this->userInfo = $this->user . ($this->pass ? ":$this->pass" : null);
    }


    /**
     * Filters the query string or fragment of a URI.
     *
     * @param string $query The raw uri query string.
     * @return string The percent-encoded query string.
     */
    protected function filter($query)
    {
        $decoded = rawurldecode($query);

        if ($decoded === $query) {
            // Query string or fragment is already decoded, encode
            return str_replace(['%3D', '%26'], ['=', '&'], rawurlencode($query));
        }

        return $query;
    }

    /**
     * Filter Uri path.
     *
     * This method percent-encodes all reserved
     * characters in the provided path string. This method
     * will NOT double-encode characters that are already
     * percent-encoded.
     *
     * @param  string $path The raw uri path.
     * @return string       The RFC 3986 percent-encoded uri path.
     * @link   http://www.faqs.org/rfcs/rfc3986.html
     */
    protected function filterPath($path)
    {
        $decoded = rawurldecode($path);

        if ($decoded === $path) {
            // Url is already decoded, encode
            return str_replace('%2F', '/', rawurlencode($path));
        }

        return $path;
    }

    /**
     * @param $scheme
     * @param $port
     * @return bool
     */
    protected function isStandardPort($scheme, $port)
    {
        if (!$scheme && !$port) {
            return true;
        }

        if (array_key_exists($scheme, $this->schemes)
            && $port === $this->schemes[$scheme]
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $key
     * @param $value
     * @return static
     */
    protected function _clone($key, $value)
    {
        if ($this->$key === $value) {
            return clone($this);
        }

        $instance = clone $this;
        $instance->$key = $value;
        return $instance;
    }
}
