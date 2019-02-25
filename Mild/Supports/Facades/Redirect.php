<?php

namespace Mild\Supports\Facades;

class Redirect extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'redirector';
    }
}