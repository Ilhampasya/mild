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

use Closure;
use Countable;
use ArrayAccess;
use ArrayIterator;
use SplFileObject;
use JsonSerializable;
use IteratorAggregate;
use ReflectionFunction;
use Mild\Supports\Traits\Dot;

class Collection implements Countable, ArrayAccess, IteratorAggregate, JsonSerializable
{
    use Dot;

    /**
     * Collection constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * @param array $items
     * @return static
     */
    public static function make($items = [])
    {
        return new static($items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param callable $callback
     * @return Collection
     */
    public function map(callable $callback)
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);
        return new static(array_combine($keys, $items));
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param null $number
     * @return Collection|mixed
     */
    public function random($number = null)
    {
        if (is_null($number)) {
            return $this->items[array_rand($this->items)];
        }
        $results = [];
        foreach (array_rand($this->items, $number) as $rand) {
            $results[] = $this->items[$rand];
        }
        return new static($results);
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->count();
    }

    /**
     * @return mixed
     */
    public function first()
    {
        foreach ($this->items as $item) {
            return $item;
        }
    }

    /**
     * @return mixed
     */
    public function middle()
    {
        return $this->items[ceil(count($this->items) / 2)];
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->count() > 0;
    }
    
    /**
     * @param string $glue
     * @return string
     */
    public function implode($glue)
    {
        $result = '';
        foreach ($this->items as $item) {
            if (is_array($item)) {
                $item = (new static($item))->implode($glue);
            }
            $result .= (string) $item.$glue;
        }
        return substr($result, 0, -strlen($glue));
    }

    /**
     * @param int $tab
     * @param bool $back
     * @return string
     * @throws \ReflectionException
     */
    public function export($tab = 2, $back = false)
    {
        $result = "array (\n";
        $repeat = str_repeat(' ', $tab);
        foreach ($this->items as $key => $value) {
            if (is_string($key)) {
                $key = '\''.$key.'\'';
            }
            if (is_string($value)) {
                $value = '\''.$value.'\'';
            } elseif (is_array($value)) {
                $value = (new static($value))->export($tab + 2, true);
            } elseif ($value instanceof Closure) {
                $spl = new SplFileObject(($ref = new ReflectionFunction($value))->getFileName());
                $spl->seek($ref->getStartLine() - 1);
                $code = '';
                for ($i = $spl->key(); $i < $ref->getEndLine(); $i++) {
                    $code .= $spl->current();
                    $spl->next();
                }
                $value = substr($code, $start = stripos($code, 'function'), (strrpos($code, '}') - $start) + 1);
            } elseif (is_object($value)) {
                $value = 'unserialize(base64_decode(\''.base64_encode(serialize($value)).'\'))';
            } else {
                $value = var_export($value, true);
            }
            $result .= $repeat.$key.' => '.$value.",\n";
        }
        if ($back) {
            return substr($result, 0, -1)."\n".str_repeat(' ', $tab - 2).')';
        }
        return substr($result, 0, -1)."\n".str_repeat(' ', 0).')';
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
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
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getItems();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * @return Collection
     */
    public function keys()
    {
        return new static(array_keys($this->items));
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
