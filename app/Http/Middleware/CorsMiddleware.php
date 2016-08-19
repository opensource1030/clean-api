<?php
namespace WA\Http\Middleware;

class CorsMiddleware {
  public function handle($request, \Closure $next)
  {
    $response = $next($request);
    $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE');
    $response->header('Access-Control-Allow-Credentials','true');
    $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
    $response->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    return $response;
  }
}
