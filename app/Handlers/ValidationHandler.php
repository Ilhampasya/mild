<?php

namespace App\Handlers;

class ValidationHandler
{
    /**
     * A list of the inputs that are never flashed.
     * 
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation'
    ];
    
    /**
     * @param \Throwable $e
     * @param \Mild\App $app
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \ReflectionException
     */
    public function handle($e, $app)
    {
        $request = $app->get('request');
        $messageBag = $e->getValidator()->getMessageBag();
        if ($request->isXhr() || $request->isJson()) {
            return new JsonResponse($messageBag->all());
        }
        return redirect()->back()->withErrors($messageBag)->withInputs($this->dontFlash);
    }
}