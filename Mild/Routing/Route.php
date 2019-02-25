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

use InvalidArgumentException;

class Route
{
    /**
     * @var array
     */
    protected $methods;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var array
     */
    protected $actions = [
        'name' => '',
        'callback' => '',
        'middleware' => []
    ];

    /**
     * Route constructor.
     * @param string $url
     * @param array $methods
     * @param array $actions
     */
    public function __construct($url, $methods, $actions)
    {
        $this->url = $url;
        $this->methods = $methods;
        if (isset($actions['name'])) {
            $this->actions['name'] = $actions['name'];
        }
        if (isset($actions['callback'])) {
            $this->actions['callback'] = $actions['callback'];
        }
        if (isset($actions['middleware'])) {
            $this->actions['middleware'] = $actions['middleware'];
        }
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $this->actions['name'] = $name;
        return $this;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }
        if (empty($this->actions['middleware'])) {
            $this->actions['middleware'] = $middleware;
        } else {
            foreach ($middleware as $value) {
                $this->actions['middleware'][] = $value;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getAction($name)
    {
        if (!isset($this->actions[$name])) {
            throw new InvalidArgumentException('Action '.$name.' does not exists.');
        }
        return $this->actions[$name];
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }
}