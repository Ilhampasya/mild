<?php

namespace Mild\Supports\Facades;

class Cache extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'cache';
    }
}