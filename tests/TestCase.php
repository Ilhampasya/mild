<?php

namespace Tests;

use Mild\App;
use App\Handlers\Handler;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @var \Mild\App
     */
    protected $app;

    protected function setUp()
    {
        new Handler($this->app = new App(dirname(__DIR__)));
    }
}