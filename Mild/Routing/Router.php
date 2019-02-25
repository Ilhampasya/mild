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

use Closure;
use Mild\Http\Response;
use InvalidArgumentException;

class Router
{
    /**
     * @var \Mild\App
     */
    protected $app;
    /**
     * @var string
     */
    protected $baseUrl;
    /**
     * @var int
     */
    protected $queue = 0;
    /**
     * @var string
     */
    protected $currentUrl;
    /**
     * @var array
     */
    protected $nameStack = [];
    /**
     * @var array
     */
    protected $routeStack = [];
    /**
     * @var array
     */
    protected $middlewareStack = [];
    /**
     * @var array
     */
    protected $groupAttributes = [
        'prefix' => '',
        'namespace' => '',
        'middleware' => []
    ];
    /**
     * @var array
     */
    protected $middlewareAliases = [];

    /**
     * Router constructor.
     * @param \Mild\App $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function get($url, $action = null)
    {
        return $this->map(['GET', 'HEAD'], $url, $action);
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function post($url, $action = null)
    {
        return $this->map(['POST'], $url, $action);
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function put($url, $action = null)
    {
        return $this->map(['PUT'], $url, $action);
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function delete($url, $action = null)
    {
        return $this->map(['DELETE'], $url, $action);
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function patch($url, $action = null)
    {
        return $this->map(['PATCH'], $url, $action);
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function options($url, $action = null)
    {
        return $this->map(['OPTIONS'], $url, $action);
    }

    /**
     * @param $url
     * @param null $action
     * @return Route
     */
    public function any($url, $action = null)
    {
        return $this->map(['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'], $url, $action);
    }

    /**
     * @param $url
     * @param null $action
     * @throws \ReflectionException
     */
    public function group($url, $action = null)
    {
        $oldGroupAttribute = $this->groupAttributes;
        if (is_string($url)) {
            if ($url !== '/' && file_exists($url)) {
                if (is_array($action)) {
                    if (isset($action['prefix'])) {
                        $this->groupAttributes['prefix'] = $action['prefix'];
                    }
                    if (isset($action['namespace'])) {
                        $this->groupAttributes['namespace'] = $action['namespace'];
                    }
                    if (isset($action['middleware'])) {
                        if (!is_array($action['middleware'])) {
                            $action['middleware'] = [$action['middleware']];
                        }
                        $this->groupAttributes['middleware'] = $action['middleware'];
                    }
                }
                require $url;
            } else {
                 $this->groupAttributes['prefix'] = trim($this->groupAttributes['prefix'], '/').'/'.trim($url, '/');
                 if (!is_array($action)) {
                     $action = ['callback' => $action, 'middleware' => []];
                 }
                 if (isset($action['namespace'])) {
                     $this->groupAttributes['namespace'] = trim($this->groupAttributes['namespace'], '\\').'\\'.trim($action['namespace'], '\\');
                 }
                 if (!is_array($action['middleware'])) {
                     $action['middleware'] = [$action['middleware']];
                 }
                 foreach ($action['middleware'] as $middleware) {
                     $this->groupAttributes['middleware'][] = $middleware;
                 }
                $this->app->call($action['callback']);
            }
        } else {
            if (!is_array($action)) {
                $action = ['middleware' => []];
            }
            if (isset($action['prefix'])) {
                $this->groupAttributes['prefix'] = trim($this->groupAttributes['prefix'], '/').'/'.trim($action['prefix'], '/');
            }
            if (isset($action['namespace'])) {
                $this->groupAttributes['namespace'] = trim($this->groupAttributes['namespace'], '\\').'\\'.trim($action['namespace'], '\\');
            }
            if (!is_array($action['middleware'])) {
                $action['middleware'] = [$action['middleware']];
            }
            foreach ($action['middleware'] as $middleware) {
                $this->groupAttributes['middleware'][] = $middleware;
            }
            $this->app->call($url);
        }
        $this->groupAttributes = $oldGroupAttribute;
    }

    /**
     * @param $methods
     * @param $url
     * @param $action
     * @return Route
     */
    protected function map($methods, $url, $action)
    {
        if (!is_array($action)) {
            $action = ['callback' => $action];
        }
        if (isset($action['callback']) && $action['callback'] instanceof Closure === false) {
            if (is_array($action['callback'])) {
                if (is_string($action['callback'][0])) {
                    $action['callback'][0] = rtrim($this->groupAttributes['namespace'], '\\').'\\'.ltrim($action['callback'][0], '\\');
                }
            } else {
                $action['callback'] = rtrim($this->groupAttributes['namespace'], '\\').'\\'.ltrim($action['callback'], '\\');
            }
        }
        $middleware = $this->groupAttributes['middleware'];
        if (empty($action['middleware'])) {
            $action['middleware'] = $middleware;
        } else {
            if (!is_array($action['middleware'])) {
                $action['middleware'] = [$action['middleware']];
            }
            foreach ($action['middleware'] as $value) {
                $middleware[] = $value;
            }
            $action['middleware'] = $middleware;
        }
        if (!empty($prefix = trim($this->groupAttributes['prefix'], '/'))) {
            $prefix = '/' .$prefix;
        }
        $url = $prefix.'/'.trim($url, '/');
        if ($url !== '/') {
            $url = rtrim($url, '/');
        }
        $this->routeStack[] = $route = new Route($url, $methods, $action);
        return $route;
    }

    /**
     * @param $name
     * @return RouterAttribute
     */
    public function name($name)
    {
        return (new RouterAttribute($this))->name($name);
    }

    /**
     * @param $prefix
     * @return RouterAttribute
     */
    public function prefix($prefix)
    {
        return (new RouterAttribute($this))->prefix($prefix);
    }

    /**
     * @param $namespace
     * @return RouterAttribute
     */
    public function namespace($namespace)
    {
        return (new RouterAttribute($this))->namespace($namespace);
    }

    /**
     * @return RouterAttribute
     */
    public function middleware()
    {
        return (new RouterAttribute($this))->middleware(...func_get_args());
    }

    /**
     * @param \Mild\Http\Request $request
     * @param \Mild\Http\Response $response
     * @return \Mild\Http\Response
     * @throws \ReflectionException
     */
    public function run($request, $response)
    {
        $server = $request->getServerParams();
        $path = implode('/', array_slice($parts = explode('/', $server['SCRIPT_NAME']), 0, -1));
        $file = end($parts);
        if (($indexFile = strpos($this->currentUrl = substr($server['REQUEST_URI'], strlen($path)), '?')) !== false) {
            $this->currentUrl = substr($this->currentUrl, 0, $indexFile);
        }
        $parts = explode('/', $this->currentUrl);
        if ($parts[1] === $file) {
            unset($parts[1]);
        }
        $this->currentUrl = '/'.trim(implode('/', $parts), '/');
        $uri = $request->getUri();
        $this->baseUrl = $uri->getScheme().'://'.$uri->getAuthority().$path;
        $found = false;
        $allowed = false;
        foreach ($this->routeStack as $route) {
            $url = $route->getUrl();
            if (!empty($name = $route->getAction('name'))) {
                $this->nameStack[$name] = $url;
            }
            if (preg_match('#^'.preg_replace('/{(.*?)}/', '(?P<$1>[\w-]+)', $url).'$#', $this->currentUrl, $matches)) {
                $found = true;
                if (in_array($request->getMethod(), $route->getMethods())) {
                    $allowed = true;
                    foreach ($route->getAction('middleware') as $middleware) {
                        $this->middlewareStack[] = $middleware;
                    }
                    $callback = $route->getAction('callback');
                    $this->middlewareStack[] = function ($request, $response) use ($callback, $matches) {
                        foreach ($matches as $k => $v) {
                            if (is_numeric($k) && $k > 0) {
                                $matches[$k - 1] = $v;
                            }
                            unset($matches[$k]);
                        }
                        ob_start();
                        if (($output = $this->app->call($callback, $matches)) instanceof Response) {
                            ob_end_clean();
                            return $output;
                        }
                        $response->getBody()->write(ob_get_clean().$output);
                        return $response;
                    };
                }
            }
        }
        if ($found === false) {
            throw new RouterException(404);
        } elseif ($allowed === false) {
            throw new RouterException(405);
        }
        return $this->resolve($request, $response);
    }

    /**
     * @param \Mild\Http\Request $request
     * @param \Mild\Http\Response $response
     * @return \Mild\Http\Response
     * @throws \ReflectionException
     */
    public function resolve($request, $response)
    {
        if (!isset($this->middlewareStack[$this->queue])) {
            return $response;
        }
        $middleware = $this->middlewareStack[$this->queue];
        $this->queue++;
        if (is_string($middleware)) {
            if (isset($this->middlewareAliases[$middleware])) {
                $middleware = $this->middlewareAliases[$middleware];
            }
            $middleware = $this->app->instance($middleware);
        }
        return $middleware($request, $response, [$this, 'resolve']);
    }

    /**
     * @param $key
     * @param array $parameters
     * @return string
     */
    public function getName($key, $parameters = [])
    {
        if (!isset($this->nameStack[$key])) {
            throw new InvalidArgumentException('Route name ['.$key.'] does not exist.');
        }
        $url = $this->nameStack[$key];
        if (!strstr($url, '{') || !strstr($url, '}')) {
            return $this->getBaseUrl($url);
        }
        if (empty($parameters)) {
            throw new InvalidArgumentException('Route name ['.$key.'] need a parameters.');
        }
        if (substr_count($url, '{') !== count($parameters)) {
            throw new InvalidArgumentException('Count parameters not same with url needle');
        }
        return $this->getBaseUrl(sprintf(preg_replace('/\{(.*?)\}/', '%s', $url), ...$parameters));
    }

    /**
     * @param string $url
     * @return string
     */
    public function getBaseUrl($url = '')
    {
        return $this->baseUrl.'/'.trim($url, '/');
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    /**
     * @return array
     */
    public function getRouteStack()
    {
        return $this->routeStack;
    }

    /**
     * @return array
     */
    public function getMiddlewareStack()
    {
        return $this->middlewareStack;
    }

    /**
     * @return array
     */
    public function getMiddlewareAliases()
    {
        return $this->middlewareAliases;
    }

    /**
     * @param $routeStack
     * @return $this
     */
    public function setRouteStack($routeStack)
    {
        $this->routeStack = $routeStack;
        return $this;
    }

    /**
     * @param $middlwareStack
     * @return $this
     */
    public function setMiddlewareStack($middlwareStack)
    {
        $this->middlewareStack = $middlwareStack;
        return $this;
    }

    /**
     * @param $middlewareAliases
     * @return $this
     */
    public function setMiddlewareAliases($middlewareAliases)
    {
        $this->middlewareAliases = $middlewareAliases;
        return $this;
    }
}