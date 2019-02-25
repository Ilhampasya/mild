<?php

namespace Mild\Supports\Facades;

class Validator extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'validator';
    }
}

