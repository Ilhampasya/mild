<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Supports;

use Mild\App;

abstract class ServiceProvider
{
    /**
     * @var App
     */
    protected $app;
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * ServiceProvider constructor.
     * @param App $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @return void
     */
    public function register()
    {
        return;
    }

    /**
     * @return void
     */
    public function boot()
    {
        return;
    }

    /**
     * @return bool
     */
    public function isDefer()
    {
        return $this->defer;
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

