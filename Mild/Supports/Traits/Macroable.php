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

use Closure;
use BadMethodCallException;

trait Macroable
{
    /**
     * @var array
     */
    protected static $macros = [];

    /**
     * @param $name
     * @return bool
     */
    public static function hasMacro($name)
    {
        return isset(static::$macros[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function getMacro($name)
    {
        if (!static::hasMacro($name)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $name
            ));
        }
        return static::$macros[$name];
    }

    /**
     * @param $name
     * @param callable $value
     * @return void
     */
    public static function setMacro($name, callable $value)
    {
        static::$macros[$name] = $value;
    }

    /**
     * @param $name
     * @return void
     */
    public static function putMacro($name)
    {
        if (static::hasMacro($name)) {
            unset(static::$macros[$name]);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $macro = static::getMacro($name);
        if ($macro instanceof Closure) {
            $macro = Closure::bind($macro, null, static::class);
        }
        return call_user_func_array($macro, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $macro = static::getMacro($name);
        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }
        return call_user_func_array($macro, $arguments);
    }
}