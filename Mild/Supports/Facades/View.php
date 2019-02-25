<?php

namespace Mild\Supports\Facades;

class View extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'view';
    }
}

