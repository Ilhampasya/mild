<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Validation;

use Mild\Supports\MessageBag;
use Mild\Supports\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
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
        $this->app->set('validator', function () {
            return new Factory(new MessageBag);
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['validator'];
    }
}