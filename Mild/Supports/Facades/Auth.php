<?php

namespace Mild\Supports\Facades;

class Auth extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'auth';
    }
}