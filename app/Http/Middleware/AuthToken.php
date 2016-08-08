<?php

namespace WA\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use WA\Repositories\Employee\EmployeeInterface;

class AuthToken
{
    /**
     * @var EmployeeInterface
     */
    protected $employee;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param EmployeeInterface $employee
     * @param Response          $response
     */
    public function __construct(EmployeeInterface $employee, Response $response)
    {
        $this->employee = $employee;
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token_payload = $request->header('X-Auth-Token');
        $employee = $this->employee->byToken($token_payload);

        if (!$token_payload || !$employee) {
            $error = [
                'code' => 'UNAUTHORIZED ACCESS',
                'http_code' => 401,
                'message' => 'You must provide a valid authentication token, see /api/token from your browser',
            ];

            return $this->response->json($error, 401);
        }

        return $next($request);
    }
}
