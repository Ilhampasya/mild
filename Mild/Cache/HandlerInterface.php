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

interface HandlerInterface
{
    /**
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     * @param int $expired
     * @return void|bool
     */
    public function set($key, $value, int $expired = 0);

    /**
     * @param $key
     * @param int $value
     * @return int|bool
     */
    public function increment($key, $value = 1);

    /**
     * @param $key
     * @param int $value
     * @return int|bool
     */
    public function decrement($key, $value = 1);

    /**
     * @param $key
     * @return void|bool
     */
    public function put($key);

    /**
     * @return void|bool
     */
    public function flush();
}