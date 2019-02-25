<?php

namespace Mild\Supports\Facades;

class Response extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'Mild\Http\Response';
    }
}

