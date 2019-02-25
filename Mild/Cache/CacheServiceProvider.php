<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Cache;

use Exception;
use Mild\Supports\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
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
        $this->app->set('cache', function ($app) {
            return new CacheManager($this->getDriver($app->get('config')->get('cache')));
        });
    }

    /**
     * @param $config
     * @return HandlerInterface
     * @throws Exception
     */
    protected function getDriver($config)
    {
        $driver = $config['default'];
        switch ($driver) {
            case 'apc':
                return new ApcHandler($config['drivers'][$driver]);
                break;
            case 'file':
                return new FileHandler($config['drivers'][$driver]);
                break;
            case 'database':
                return new DatabaseHandler($this->app->get('db'), $config['drivers'][$driver]);
                break;
            case 'redis':
                return new RedisHandler($config['drivers'][$driver]);
                break;
            case 'memcache':
                return new MemcacheHandler($config['drivers'][$driver]);
                break;
            case 'memcached':
                return new MemcachedHandler($config['drivers'][$driver]);
                break;
            default:
                throw new Exception('Unsupported session driver ['.$driver.']');
                break;
        }
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['cache'];
    }
}