<?php

namespace App\Bootstraps;

class RegisterFacade
{
    /**
     * @param \Mild\App $app
     * @param callable $next
     * @throws \ReflectionException
     * @return \Mild\App
     */
    public function bootstrap($app, $next)
    {
        return $next($app->facades($app->get('config')->get('app.aliases', [])));
    }
}