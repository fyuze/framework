<?php
namespace Fyuze\Routing;

use Fyuze\Http\Request;

class Matcher
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Route
     */
    protected $route;

    /**
     * @param Request $request
     * @param Route $route
     */
    public function __construct(Request $request, Route $route)
    {
        $this->request = $request;
        $this->route = $route;
    }

    /**
     * @return bool
     */
    public function resolves()
    {
        if (preg_match($this->compileRegex(), $this->request->getPath(), $parameters) > 0) {

            $params = [];

            foreach ($parameters as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }

            $this->request->setParams($params);

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

        return "%^{$route}$%s";
    }
}