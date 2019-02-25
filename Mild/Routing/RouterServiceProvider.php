<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Routing;

use Mild\Supports\ServiceProvider;

class RouterServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->set('router', function ($app) {
            return new Router($app);
        });
        $this->app->set('redirector', function ($app) {
            return new Redirector($app->get('flash'), $app->get('router'), $app->get('request'));
        });
    }
}