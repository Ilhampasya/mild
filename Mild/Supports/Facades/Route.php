<?php

namespace Mild\Supports\Facades;

class Route extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'router';
    }
}

