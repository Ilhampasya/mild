<?php

namespace App\Providers;

use Faker\Factory;
use Mild\Supports\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->set('faker', function ($app) {
            return Factory::create($app->get('config')->get('app.locale', 'id_ID'));
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        //
    }
}

