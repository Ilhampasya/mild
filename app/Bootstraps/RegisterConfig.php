<?php

namespace App\Bootstraps;

use Carbon\Carbon;
use Mild\Config\Repository;

class RegisterConfig
{
    /**
     * Set configuration path
     *
     * @var string
     */
    protected $path = 'config';

    /**
     * @param \Mild\App $app
     * @param callable $next
     * @return \Mild\App
     */
    public function bootstrap($app, $next)
    {
        if (file_exists($cached = $app->getConfigCachePath())) {
            $items = require $cached;
        } else {
            $items = $this->load($app->getPath($this->path));
        }
        $app->set('config', $config = new Repository($items));
        date_default_timezone_set($config->get('app.timezone', 'Asia/Jakarta'));
        mb_internal_encoding('UTF-8');
        Carbon::setLocale($config->get('app.locale', 'id'));
        return $next($app);
    }

    /**
     * @param $path
     * @return array
     */
    protected function load($path)
    {
        $items = [];
        $handler = opendir($path);
        while (false !== ($file = readdir($handler))) {
            if ($file !== '.' && $file !== '..') {
                $key = basename($file, '.php');
                $file = $path.'/'.$file;
                if (is_dir($file)) {
                    $items[$key] = $this->load($file);
                } else {
                    $items[$key] = require $file;
                }
            }
        }
        closedir($handler);
        return $items;
    }
}