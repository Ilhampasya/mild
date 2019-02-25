<?php

namespace App\Http;

use Throwable;
use Mild\Http\Request;
use Mild\Http\Response;
use App\Bootstraps\RegisterConfig;
use App\Bootstraps\RegisterFacade;
use App\Bootstraps\RegisterProvider;
use App\Http\Middleware\ValidatePostSizeMiddleware;

class Kernel
{
    /**
     * The bootstrap index
     *
     * @var int
     */
    protected $queue = 0;
    /**
     * Register Bootstrap application
     *
     * @var array
     */
    protected $bootstraps = [
        RegisterConfig::class,
        RegisterFacade::class,
        RegisterProvider::class
    ];
    /**
     * Register global middleware stack on the application
     * 
     * @var array
     */
    protected $middleware = [
        ValidatePostSizeMiddleware::class,
    ];
    /**
     * Set aliases on calling middleware
     *
     * @var array
     */
    protected $middlewareAliases = [
        'api' => Middleware\ApiMiddleware::class,
        'web' => Middleware\WebMiddleware::class
    ];
    /**
     * Kernel constructor.
     *
     * @param \App\Handlers\Handler $handler
     * @throws Throwable
     */
    public function __construct($handler)
    {
        try {
            $app = $this->bootstrap($handler->getApp());
            $app->set('response', $response = new Response);
            $app->set('request',  $request = Request::capture());
            $app->set(Request::class, function ($app) {
                return $app->get('request');
            });
            $response = $app->get('router')->setMiddlewareStack($this->middleware)->setMiddlewareAliases($this->middlewareAliases)->run($request, $response);
        } catch (Throwable $e) {
            $response = $handler->handle($e);
        }
        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();
        $protocolVersion = $response->getProtocolVersion();
        if (headers_sent() === false) {
            header('HTTP/'.$protocolVersion.' '.$statusCode.' '.$reasonPhrase.'', true, $statusCode);
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header($name.': '.$value, false);
                }
            }
        }
        $stream = $response->getBody();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }
        while (!$stream->eof()) {
            echo $stream->read(8192);
            if (connection_status() !== 0) {
                break;
            }
        }
    }

    /**
     * @param $app
     * @return \Mild\App
     */
    public function bootstrap($app)
    {
        $queue = $this->queue;
        $this->queue++;
        if (!isset($this->bootstraps[$queue])) {
            return $app;
        }
        return (new $this->bootstraps[$queue])->bootstrap($app, [$this, 'bootstrap']);
    }
}