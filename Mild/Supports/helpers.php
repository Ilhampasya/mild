<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
use Mild\App;
use Carbon\Carbon;
use Mild\Supports\Collection;

/**
 * @param null $id
 * @return App|mixed|object
 * @throws ReflectionException
 */
function app($id = null)
{
    $app = App::getInstance();
    if (is_null($id)) {
        return $app;
    }
    return $app->get($id);
}

/**
 * @param string $name
 * @return string
 * @throws ReflectionException
 */
function path($name = '')
{
    return app()->getPath($name);
}

/**
 * @param $value
 * @return mixed
 * @throws ReflectionException
 */
function encrypt($value)
{
    return app('encryption')->encrypt($value);
}

/**
 * @param $value
 * @return mixed
 * @throws ReflectionException
 */
function decrypt($value)
{
    return app('encryption')->decrypt($value);
}

/**
 * @param string $url
 * @return mixed
 * @throws ReflectionException
 */
function url($url = '')
{
    return app('router')->getBaseUrl($url);
}

/**
 * @param null $name
 * @param array $parameters
 * @return App|mixed|object
 * @throws ReflectionException
 */
function route($name = null, $parameters = [])
{
    $router = app('router');
    if ($name === null) {
        return $router;
    }
    return $router->getName($name, $parameters);
}

/**
 * @param null $key
 * @param null $default
 * @return App|mixed|object
 * @throws ReflectionException
 */
function config($key = null, $default = null)
{
    $config = app('config');
    if ($key === null) {
        return $config;
    }
    return $config->get($key, $default);
}

/**
 * @param array $items
 * @return Collection
 */
function collect($items = [])
{
    return new Collection($items);
}

/**
 * @param $value
 * @param bool $doubleEncode
 * @return string
 */
function e($value, $doubleEncode = true)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
}

/**
 * @return mixed|object
 * @throws ReflectionException
 */
function csrf_token()
{
    return app('session')->get('_token');
}

/**
 * @return string
 * @throws ReflectionException
 */
function csrf_field()
{
    return '<input type=\'hidden\' name=\'_token\' value=\''.csrf_token().'\'/>';
}

/**
 * @return App|mixed
 * @throws Exception
 */
function response()
{
    return app()->instance('Mild\Http\Response');
}

/**
 * @return App|mixed
 * @throws Exception
 */
function request()
{
    return app('request');
}

/**
 * @return App|mixed
 * @throws Exception
 */
function validator()
{
    return app('validator');
}

/**
 * @param null $key
 * @return \Mild\Cookie\CookieJar
 * @throws ReflectionException
 */
function cookie($key = null)
{
    $cookie = app('cookie');
    if ($key !== null) {
        return $cookie->get($key);
    }
    return $cookie;
}

/**
 * @param null $key
 * @return \Mild\Session\SessionManager
 * @throws \ReflectionException
 */
function session($key = null)
{
    $session = app('session');
    if ($key !== null) {
        return $session->get($key);
    }
    return $session;
}

/**
 * @param $method
 * @return string
 */
function method_field($method)
{
    return '<input type="hidden" name="_method" value="'.$method.'" />';
}

/**
 * @param $name
 * @return mixed
 */
function old($name)
{
    return Flash::get('inputs.'.$name.'');
}


/**
 * @param int $code
 * @param null $reasonPhrase
 */
function abort($code = 404, $reasonPhrase = null)
{
    throw new \Mild\Routing\RouterException($code, $reasonPhrase);
}

/**
 * @param null $url
 * @param int $status
 * @param array $headers
 * @return \Mild\Http\RedirectResponse
 * @throws ReflectionException
 */
function redirect($url = null, $status = 302, $headers = [])
{
    $redirector = app('redirector');
    if (is_null($url)) {
        return $redirector;
    }
    return $redirector->to($url, $status, $headers);
}

/**
 * @param $value
 * @param $callback
 * @return mixed
 */
function tap($value, $callback)
{
    $callback($value);
    return $value;
}

/**
 * @param \DateTimeZone|null $tz
 * @return Carbon|\Carbon\CarbonInterface
 */
function today($tz = null)
{
    return Carbon::today($tz);
}

/**
 * @param \DateTimeZone|null$tz
 * @return Carbon|\Carbon\CarbonInterface
 */
function now($tz = null)
{
    return Carbon::now($tz);
}