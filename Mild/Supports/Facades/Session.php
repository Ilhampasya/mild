<?php

namespace Mild\Supports\Facades;

class Session extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'session';
    }
}

