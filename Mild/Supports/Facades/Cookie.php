<?php

namespace Mild\Supports\Facades;

class Cookie extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'cookie';
    }
}