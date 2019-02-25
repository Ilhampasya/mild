<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Supports\Traits;

trait Dot
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        if (isset($this->items[$key])) {
            return true;
        }
        $items = $this->items;
        $segments = explode('.', $key);
        $key = array_pop($segments);
        foreach ($segments as $segment) {
            if (isset($items[$segment])) {
                $items =& $items[$segment];
            }
        }
        return isset($items[$key]);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param $key
     * @param null $default
     * @return array|null
     */
    public function get($key, $default = null)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        $items = $this->items;
        foreach (explode('.', $key) as $segment) {
            if (!isset($items[$segment])) {
                return $default;
            }
            $items = $items[$segment];
        }
        return $items;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        $items =& $this->items;
        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (!isset($items[$segment])) {
               $items[$segment] = [];
            }
            if (!is_array($items[$segment])) {
                $items[$segment] = [$items[$segment]];
            }
            $items =& $items[$segment];
        }
        $items = $value;
    }

    /**
     * @param $key
     * @return void
     */
    public function put($key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
            return;
        }
        $items =& $this->items;
        $segments = explode('.', $key);
        $key = array_pop($segments);
        foreach ($segments as $segment) {
            if (isset($items[$segment])) {
                $items =& $items[$segment];
            }
        }
        unset($items[$key]);
    }


    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}

