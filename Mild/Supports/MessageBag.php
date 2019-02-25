<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Supports;

use Countable;
use ArrayAccess;
use JsonSerializable;
use Mild\Supports\Traits\Dot;

class MessageBag implements Countable, ArrayAccess, JsonSerializable
{
    use Dot;

    /**
     * MessageBag constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * @param $items
     * @return void
     */
    public function add($items)
    {
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->count();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function first($key)
    {
        $items = $this->get($key);
        if (is_array($items)) {
            foreach ($items as $item) {
                return $item;
            }
        }
        return $items;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function middle($key)
    {
        $items = $this->get($key);
        if (is_array($items)) {
            return $items[ceil(count($items) / 2)];
        }
        return $items;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function last($key)
    {
        $items = $this->get($key);
        if (is_array($items)) {
            return end($items);
        }
        return $items;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
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
     * @return mixed
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
}

