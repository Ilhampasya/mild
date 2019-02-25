<?php

namespace App\Http\Middleware;

use Mild\App;
use Mild\Routing\RouterException;

class WebMiddleware
{
    /**
     * @var App
     */
    protected $app;
    /**
     * @var \Mild\Session\SessionManager
     */
    protected $session;
    /**
     * @var \Mild\Encryption\Encryption
     */
    protected $encryption;
    /**
     * @var array
     */
    protected $excepts = [
        //
    ];

    /**
     * WebMiddleware constructor.
     * @param \Mild\App $app
     * @throws \Exception
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->session = $app->get('session');
        $this->encryption = $app->get('encryption');
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function __invoke($request, $response, $next)
    {
        if (is_null($token = $this->session->get('_token'))) {
            $this->session->set('_token', $this->encryption->encrypt(time()));
        }
        if ($this->isReading($request->getMethod()) || $this->isExcept() || $this->isMatchToken($request, $token)) {
            return $next($request, $response);
        }
        throw new RouterException(419);
    }

    /**
     * @param $method
     * @return bool
     */
    protected function isReading($method)
    {
        return in_array($method, ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function isExcept()
    {
        $uri = $this->app->get('router')->getCurrentUrl();
        foreach ($this->excepts as $except) {
            $except = trim($except, '/');
            $except = $except ? '/' .$except : $except;
            $except = preg_quote($except, '#');
            $except = str_replace('\*', '.*', $except);
            if (preg_match('#^'.$except.'\z#u', $uri, $matches)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $request
     * @param $token
     * @return bool
     */
    protected function isMatchToken($request, $token)
    {
        return ($request->getParsedBodyParam('_token') ?: $request->getHeaderLine('X-CSRF-TOKEN')) === $token;
    }
}

