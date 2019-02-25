<?php

namespace Mild\Supports\Facades;

use App\Console\Kernel;

class Symfony extends Facade
{
    protected static function setFacadeRoot()
    {
        return Kernel::class;
    }
}