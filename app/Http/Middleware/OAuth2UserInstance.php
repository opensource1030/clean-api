<?php

namespace WA\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use WA\Repositories\User\UserInterface;

class AuthToken
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param UserInterface $user
     * @param Response          $response
     */
    public function __construct(UserInterface $user, Response $response)
    {
        $this->employee = $user;
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
        $user = $this->employee->byToken($token_payload);

        if (!$token_payload || !$user) {
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
