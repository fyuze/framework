<?php
namespace Fyuze\Routing;

class Matcher
{
    /**
     * @param Route $route
     * @param $url
     */
    public function __construct(Route $route, $url)
    {
        $this->route = $route;
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function resolves()
    {
        return (bool) preg_match($this->compileRegex(), $this->url, $parameters);
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
