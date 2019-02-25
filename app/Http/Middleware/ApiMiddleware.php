<?php

namespace App\Http\Middleware;

class ApiMiddleware
{
    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function __invoke($request, $response, $next)
    {
        return $next($request, $response);
    }
}

