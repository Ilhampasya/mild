<?php

namespace Mild\Supports\Facades;

class Faker extends Facade
{
    protected static function setFacadeRoot()
    {
        return 'faker';
    }
}