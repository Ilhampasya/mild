<?php

namespace Mild\Supports\Facades;

class Flash extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'flash';
    }
}

