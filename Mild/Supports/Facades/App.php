<?php

namespace Mild\Supports\Facades;

class App extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'app';
    }
}

