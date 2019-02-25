<?php

namespace Mild\Supports\Facades;

class Log extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'logger';
    }
}