<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild;

use Closure;
use ArrayAccess;
use ReflectionClass;
use RuntimeException;
use ReflectionMethod;
use ReflectionFunction;
use Mild\Log\LogServiceProvider;
use Mild\Supports\Facades\Facade;
use Mild\Supports\ServiceProvider;

class App implements ArrayAccess
{
    /**
     * @var string
     */
    protected $basePath;
    /**
     * @var array
     */
    protected $facades = [];
    /**
     * @var array
     */
    protected $bindings = [];
    /**
     * @var bool
     */
    protected $booted = false;
    /**
     * @var static
     */
    protected static $instance;
    /**
     * Application version
     * @var string
     */
    protected $version = '1.0.0';
    /**
     * @var string
     */
    protected $routeCachePath = 'storage/cache/route.php';
    /**
     * @var string
     */
    protected $configCachePath = 'storage/cache/config.php';
    /**
     * @var array
     */
    protected $providerStack = ['defers' => [], 'registered' => []];

    /**
     * App constructor.
     * @param string $basePath
     */
    public function __construct($basePath = '')
    {
        static::$instance = $this;
        if ($basePath) {
            $this->setBasePath($basePath);
        }
        $this->set('app', function ($app) {
           return $app;
        });
        $this->set(static::class, function ($app) {
            return $app;
        });
        $this->register(new LogServiceProvider($this));
    }

    /**
     * @param $path
     * @return void
     */
    public function setBasePath($path)
    {
        $this->basePath = rtrim($path, '\/');
    }

    /**
     * @param $path
     * @return void
     */
    public function setRouteCachePath($path)
    {
        $this->routeCachePath = $path;
    }

    /**
     * @return string
     */
    public function getRouteCachePath()
    {
        return $this->getPath($this->routeCachePath);
    }

    /**
     * @param $path
     */
    public function setConfigCachePath($path)
    {
        $this->configCachePath = $path;
    }

    /**
     * @return string
     */
    public function getConfigCachePath()
    {
        return $this->getPath($this->configCachePath);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getPath($name = '')
    {
        if (!empty($name)) {
            $name = '/'.$name;
        }
        return $this->getBasePath().$name;
    }

    /**
     * @return App
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function runningInConsole()
    {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }

    /**
     * @param ServiceProvider $provider
     * @param bool $defer
     * @return void
     */
    public function register($provider, $defer = true)
    {
        if ($provider instanceof ServiceProvider === false) {
            $provider = new $provider($this);
        }
        if ($defer === true && $provider->isDefer() === true) {
            foreach ($provider->provides() as $p) {
                $this->providerStack['defers'][$p] = $provider;
            }
            return;
        }
        $provider->register();
        $this->providerStack['registered'][] = $provider;
        if ($this->booted) {
            $provider->boot();
        }
    }

    /**
     * @param array $facades
     * @return $this
     */
    public function facades($facades = [])
    {
        Facade::setApp($this);
        $this->facades = $facades;
        spl_autoload_register([$this, 'loadFacade'], true, true);
        return $this;
    }

    /**
     * @param $facade
     * @return bool
     */
    public function loadFacade($facade)
    {
        if (isset($this->facades[$facade])) {
            return class_alias($this->facades[$facade], $facade);
        }
        if (strpos($facade, 'Facades\\') === 0) {
            if (file_exists($path = $this->getPath('storage/cache/').sha1($facade).'.php')) {
                require $path;
                return true;
            }
            $namespace = explode('\\', $facade);
            $name = array_pop($namespace);
            $namespace = implode('\\', $namespace);
            $target = substr($facade, 8);
            $stub = <<<EOF
<?php

namespace $namespace;

use Mild\Supports\Facades\Facade;

class $name extends Facade
{
    protected static function setFacadeRoot()
    {
        return '$target';
    }
}
EOF;
            file_put_contents($path, $stub);
            require $path;
            return true;
        }
        return false;
    }

    /**
     * @param $providers
     * @return $this
     */
    public function providers($providers)
    {
        foreach ($providers as $key => $provider) {
            $this->register($provider);
        }
        foreach ($this->providerStack['registered'] as $p) {
            $p->boot();
        }
        $this->booted = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getProviderStack()
    {
        return $this->providerStack;
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @param ReflectionMethod|ReflectionFunction $reflector
     * @param array $parameters
     * @return array
     * @throws \ReflectionException
     */
    public function dependencies($reflector, $parameters = [])
    {
        $index = 0;
        $dependencies = [];
        foreach ($reflector->getParameters() as $parameter) {
            $position = $parameter->getPosition();
            if ($class = $parameter->getClass()) {
                $dependencies[$position] = $this->instance($class);
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[$position] = $parameter->getDefaultValue();
            } elseif (isset($parameters[$index])) {
                $dependencies[$position] = $parameters[$index];
                $index++;
            }
        }
        return $dependencies;
    }

    /**
     * @param $callable
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public function call($callable, $parameters = [])
    {
        if (is_string($callable) && strpos($callable, '@') !== false) {
            $callable = explode('@', $callable);
            $callable[0] = $this->instance($callable[0]);
        }
        return call_user_func_array($callable, $this->dependencies(is_array($callable) ? new ReflectionMethod($callable[0], $callable[1]) : new ReflectionFunction($callable) , $parameters));
    }

    /**
     * @param $class
     * @param array $parameters
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function instance($class, $parameters = [])
    {
        $class = $this->resolveReflectionClassInstance($class);
        if ($this->has($name = $class->getName())) {
            return $this->get($name);
        }
        return $class->newInstanceArgs(!empty($constructor = $class->getConstructor()) ? $this->dependencies($constructor, $parameters) : $parameters);
    }

    /**
     * @param $class
     * @return ReflectionMethod
     * @throws \ReflectionException
     */
    public function getConstructor($class)
    {
        return $this->resolveReflectionClassInstance($class)->getConstructor();
    }

    /**
     * @param $class
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    protected function resolveReflectionClassInstance($class)
    {
        if ($class instanceof ReflectionClass === false) {
            return new ReflectionClass($class);
        }
        return $class;
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->bindings[$id]);
    }

    /**
     * @param $id
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function get($id)
    {
        if (isset($this->providerStack['defers'][$id])) {
            $this->register($this->providerStack['defers'][$id], false);
            unset($this->providerStack['defers'][$id]);
        }
        if (!$this->has($id)) {
            throw new RuntimeException('Binding '.$id.' does not exist.');
        }
        if (is_string($binding = $this->bindings[$id])) {
            $this->put($id);
            $this->set($id, $binding = $this->instance($binding));
        } elseif ($binding instanceof Closure) {
            $this->put($id);
            $this->set($id, $binding = $binding($this));
        }
        return $binding;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->bindings;
    }

    /**
     * @param $id
     * @param $value
     */
    public function set($id, $value)
    {
        $this->bindings[$id] = $value;
    }

    /**
     * @param $id
     * @return void
     */
    public function put($id)
    {
        unset($this->bindings[$id]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->put($offset);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param $name
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        $this->put($name);
    }
}
