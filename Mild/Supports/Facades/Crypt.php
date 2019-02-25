<?php

namespace Mild\Supports\Facades;

class Crypt extends Facade
{
    /**
     * @return string
     */
    protected static function setFacadeRoot()
    {
        return 'encryption';
    }
}

