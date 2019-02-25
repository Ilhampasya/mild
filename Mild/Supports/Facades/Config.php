<?php

namespace Mild\Supports\Facades;

class Config extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'config';
    }
}

