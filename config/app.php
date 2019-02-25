<?php
return [
    'name' => 'Mild',
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id_ID',
    'aliases' => [
        'App' => \Mild\Supports\Facades\App::class,
        'Cache' => \Mild\Supports\Facades\Cache::class,
        'Config' => \Mild\Supports\Facades\Config::class,
        'Cookie' => \Mild\Supports\Facades\Cookie::class,
        'Crypt' => \Mild\Supports\Facades\Crypt::class,
        'DB' => \Mild\Supports\Facades\DB::class,
        'Flash' => \Mild\Supports\Facades\Flash::class,
        'Faker' => \Mild\Supports\Facades\Faker::class,
        'Log' => \Mild\Supports\Facades\Log::class,
        'Redirect' => \Mild\Supports\Facades\Redirect::class,
        'Response' => \Mild\Supports\Facades\Response::class,
        'Request' => \Mild\Supports\Facades\Request::class,
        'Route' => \Mild\Supports\Facades\Route::class,
        'Session' => \Mild\Supports\Facades\Session::class,
        'Symfony' => \Mild\Supports\Facades\Symfony::class,
        'Validator' => \Mild\Supports\Facades\Validator::class,
        'View' => \Mild\Supports\Facades\View::class
    ],
    'providers' => [
        \Mild\Views\ViewServiceProvider::class,
        \Mild\Cache\CacheServiceProvider::class,
        \Mild\Cookie\CookieServiceProvider::class,
        \Mild\Session\SessionServiceProvider::class,
        \Mild\Database\DatabaseServiceProvider::class,
        \Mild\Encryption\EncryptionServiceProvider::class,
        \Mild\Validation\ValidationServiceProvider::class,
        \App\Providers\AppServiceProvider::class,
        \App\Providers\RouteServiceProvider::class
    ]
];

