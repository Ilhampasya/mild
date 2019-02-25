<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Routing;

class RouterAttribute
{
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'prefix' => '',
        'namespace' => '',
        'middleware' => [],
    ];

    /**
     * RouterAttribute constructor.
     * @param Router $router
     */
    public function __construct($router)
    {
        $this->router = $router;
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function get($url, $action = null)
    {
        return $this->router->get($url, $this->resolve($action));
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function post($url, $action = null)
    {
        return $this->router->post($url, $this->resolve($action));
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function put($url, $action = null)
    {
        return $this->router->patch($url, $this->resolve($action));
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function delete($url, $action = null)
    {
        return $this->router->delete($url, $this->resolve($action));
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function patch($url, $action = null)
    {
        return $this->router->patch($url, $this->resolve($action));
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function options($url, $action = null)
    {
        return $this->router->options($url, $this->resolve($action));
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function any($url, $action = null)
    {
        return $this->router->any($url, $this->resolve($action));
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    /**
     * @param $prefix
     * @return $this
     */
    public function prefix($prefix)
    {
        $this->attributes['prefix'] = $prefix;
        return $this;
    }

    /**
     * @param $namespace
     * @return $this
     */
    public function namespace($namespace)
    {
        $this->attributes['namespace'] = $namespace;
        return $this;
    }

    /**
     * @return $this
     */
    public function middleware()
    {
        $middleware = func_get_args();
        if (isset($middleware[0]) && is_array($middleware[0])) {
            $middleware = $middleware[0];
        }
        if (!empty($this->attributes['middleware'])) {
            foreach ($middleware as $value) {
                $this->attributes['middleware'][] = $value;
            }
        } else {
            $this->attributes['middleware'] = $middleware;
        }
        return $this;
    }

    /**
     * @param $router
     * @throws \ReflectionException
     */
    public function group($router)
    {
        $this->router->group($this->attributes, $router);
    }

    /**
     * @param $action
     * @return array
     */
    protected function resolve($action)
    {
        if (!is_array($action)) {
            $action = ['callback' => $action];
        }
        if (!isset($action['name'])) {
            $action['name'] = $this->attributes['name'];
        }
        if (!isset($action['prefix'])) {
            $action['prefix'] = $this->attributes['prefix'];
        }
        if (!isset($action['namespace'])) {
            $action['namespace'] = $this->attributes['namespace'];
        }
        if (!isset($action['middleware'])) {
            $action['middleware'] = $this->attributes['middleware'];
        }
        return $action;
    }
}