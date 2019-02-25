<?php

namespace App\Handlers;

use Mild\Http\Response;

class RouterHandler
{
    /**
     * @param \Throwable $e
     * @param \Mild\App $app
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \ReflectionException
     */
    public function handle($e, $app)
    {
        return $app->get('view')->renderResponse($app->get('response')->withStatus($code = $e->getCode(), $e->getMessage()), 'errors/'.$code.'.mld');
    }
}