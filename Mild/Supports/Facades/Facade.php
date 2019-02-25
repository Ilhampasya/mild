<?php

namespace Mild\Supports\Facades;

use RuntimeException;

abstract class Facade
{
    /**
     * @var \Mild\App
     */
    protected static $app;

    /**
     * @return string
     * @throws \RunTimeException
     */
    protected static function setFacadeRoot()
    {
        throw new RuntimeException('Facade does not implement setFacadeRoot method.');
    }

    /**
     * @param \Mild\App $app
     * @return void
     */
    public static function setApp($app)
    {
        static::$app = $app;
    }

    /**
     * @return \Mild\App
     */
    public static function getApp()
    {
        return static::$app;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getFacadeRoot()
    {
        if (static::$app->has($root = static::setFacadeRoot()) || isset(static::$app->getProviderStack()['defers'][$root])) {
            return static::$app->get($root);
        }
        return static::$app->instance($root);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        return static::getFacadeRoot()->$name(...$arguments);
    }
}

