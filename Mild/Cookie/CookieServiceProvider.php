<?php

namespace Mild\Cookie;

use Mild\Supports\ServiceProvider;

class CookieServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;
    /**
     * @return void
     */
    public function register()
    {
        $this->app->set('cookie', function ($app) {
           return new CookieJar($app->get('encryption'));
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['cookie'];
    }
}