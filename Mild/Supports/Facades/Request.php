<?php

namespace Mild\Supports\Facades;

class Request extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'request';
    }
}

