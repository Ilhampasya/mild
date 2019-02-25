<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Log;

use Mild\Http\Stream;
use Psr\Log\LoggerInterface;
use Mild\Supports\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->set('logger', function ($app) {
            $config = $app->get('config')->get('logger');
            if (isset($config['path'])) {
                $path = $config['path'];
            } else {
                $path = 'php://stderr';
            }
            if (isset($config['channel'])) {
                $channel = $config['channel'];
            } else {
                $channel = null;
            }
            if (isset($config['minLevel'])) {
                $minLevel = $config['minLevel'];
            } else {
                $minLevel = null;
            }
            return new Logger(new Stream(fopen($path, 'a')), $channel, $minLevel);
        });
        $this->app->set(LoggerInterface::class, function ($app) {
           return $app->get('logger');
        });
    }
}