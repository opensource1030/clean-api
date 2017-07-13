<?php

namespace WA\Http\Middleware;

class CorsMiddleware
{
    public function handle($request, \Closure $next)
    {
    	if ($request->getMethod() != 'OPTIONS') {
    		$response = $next($request);
    	} else {
    		$response = response("", 200);
    	}

        $response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, PATCH, DELETE');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
        $response->headers->set('Access-Control-Allow-Origin', env('FRONTEND_DOMAIN', '*'));

        return $response;
    }
}
