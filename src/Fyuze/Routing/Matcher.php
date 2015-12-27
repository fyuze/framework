<?php
namespace Fyuze\Routing;

use Psr\Http\Message\ServerRequestInterface;

class Matcher
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var Route
     */
    protected $route;

    /**
     * @param ServerRequestInterface $request
     * @param Route $route
     */
    public function __construct(ServerRequestInterface $request, Route $route)
    {
        $this->request = $request;
        $this->route = $route;
    }

    /**
     * @return bool
     */
    public function resolves()
    {
        if (preg_match($this->compileRegex(), $this->request->getUri()->getPath(), $parameters) > 0) {

            $params = [];

            foreach ($parameters as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }

            $this->request = $this->request->withQueryParams($params);

            return true;
        }

        return false;
    }

    /**
     * Returns the regex needed to match the route.
     *
     * @access  public
     * @return  string
     */
    public function compileRegex()
    {
        $route = $this->route->getUri();

        if (strpos($route, '?')) {
            $route = preg_replace('@\/{([\w]+)\?}@', '(?:/{$1})?', $route);
        }

        $route = preg_replace('/{([a-z0-9_-]+)}/i', '(?P<$1>[^/]+)', $route);

        if(substr($route, -1) === '/') {
            $route .= '?';
        }

        return "%^{$route}$%s";
    }
}
