<?php

namespace WA\Http\Middleware;

class ScopeTestMiddleware
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
