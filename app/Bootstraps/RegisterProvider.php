<?php

namespace App\Bootstraps;

class RegisterProvider
{

    /**
     * Register Service Providers
     *
     * @param \Mild\App $app
     * @param callable $next
     * @throws \ReflectionException
     * @return \Mild\App
     */
    public function bootstrap($app, $next)
    {
        return $next($app->providers($app->get('config')->get('app.providers', [])));
    }
}