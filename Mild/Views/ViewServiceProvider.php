<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Views;

use Mild\Supports\ViewErrorBag;
use Mild\Supports\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->set('view', function ($app) {
            return new View($app->get('config')->get('view'));
        });
    }

    /**
     * @throws \ReflectionException
     */
    public function boot()
    {
        $view = $this->app->get('view');
        $view->share('app', $this->app);
        $view->share('errors', new ViewErrorBag($this->app->get('flash')->get('errors')));
    }
}