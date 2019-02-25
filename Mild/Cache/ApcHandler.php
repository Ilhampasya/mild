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

class ApcHandler implements HandlerInterface
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * ApcHandler constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (isset($config['prefix'])) {
            $this->setPrefix($config['prefix']);
        }
    }

    /**
     * @param $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $key = $this->getName($key);
        return function_exists('apcu_exists') ? apcu_exists($key) : apc_exists($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $key = $this->getName($key);
        return function_exists('apcu_fetch') ? apcu_fetch($key) : apc_fetch($key);
    }

    /**
     * @param $key
     * @param $value
     * @param int $expired
     * @return mixed
     */
    public function set($key, $value, int $expired = 0)
    {
        $key = $this->getName($key);
        $expired = $expired * 60;
        return function_exists('apcu_add') ? apcu_add($key, $value, $expired) : apc_add($key, $value, $expired);
    }

    /**
     * @param $key
     * @return void
     */
    public function put($key)
    {
        $key = $this->getName($key);
        return function_exists('apcu_delete') ? apcu_delete($key) : apc_delete($key);
    }

    /**
     * @return void
     */
    public function flush()
    {
        return function_exists('apcu_clear_cache') ? apcu_clear_cache() : apc_clear_cache('user');
    }

    /**
     * @param $key
     * @param int $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        $key = $this->getName($key);
        return function_exists('apcu_inc') ? apcu_inc($key, $value) : apc_inc($key, $value);
    }

    /**
     * @param $key
     * @param int $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return function_exists('apcu_dec') ? apcu_dec($key, $value) : apc_dec($key, $value);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param $key
     * @return string
     */
    protected function getName($key)
    {
        return $this->prefix.$key;
    }
}