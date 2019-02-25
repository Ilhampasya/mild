<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Encryption;

use Mild\Supports\ServiceProvider;

class EncryptionServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * @return void
     */
    public function register()
    {
        $this->app->set('encryption', function ($app) {
            $config = $app->get('config');
            $key = $config->get('encryption.key');
            $cipher = $config->get('encryption.cipher');
            return new Encryption($key, $cipher);
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['encryption'];
    }
}