<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Database;

use Mild\Supports\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->set('db', function ($app) {
            return new Database($app->get('config')->get('database'));
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        Model::setApp($this->app);
    }
}