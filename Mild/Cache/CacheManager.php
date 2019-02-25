<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Cache;

class CacheManager
{
    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * CacheManager constructor.
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->handler->has($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->handler->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @param int $expired
     * @return bool|void
     */
    public function set($key, $value, int $expired = 0)
    {
        return $this->handler->set($key, $value, $expired);
    }

    /**
     * @param $key
     * @return bool|void
     */
    public function put($key)
    {
        return $this->handler->put($key);
    }

    /**
     * @param $key
     * @param int $value
     * @return bool|int
     */
    public function increment($key, $value = 1)
    {
        return $this->handler->increment($key, $value);
    }

    /**
     * @param $key
     * @param int $value
     * @return bool|int
     */
    public function decrement($key, $value = 1)
    {
        return $this->handler->decrement($key, $value);
    }

    /**
     * @return bool|void
     */
    public function flush()
    {
        return $this->handler->flush();
    }

    /**
     * @param $key
     * @param $expired
     * @param callable $callable
     * @return mixed
     */
    public function remember($key, $expired, callable $callable)
    {
        $value = $this->handler->get($key);
        if (!$value) {
            $value = $callable();
            $this->handler->set($key, $value, $expired);
        }
        return $value;
    }

    /**
     * @return HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->handler->$name(...$arguments);
    }
}