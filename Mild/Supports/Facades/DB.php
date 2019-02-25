<?php

namespace Mild\Supports\Facades;

class DB extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'db';
    }
}