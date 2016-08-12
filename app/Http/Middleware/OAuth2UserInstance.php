<?php

namespace WA\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use LucaDegasperi\OAuth2Server\Authorizer;
use WA\Repositories\User\UserInterface;

class OAuth2UserInstance
{
    /**
     * @var UserInterface
     */
    protected $user;


    /**
     * @var Authorizer
     */
    protected $authorizer;


    /**
     * AuthToken constructor.
     * @param UserInterface $user
     * @param Authorizer $authorizer
     */
    public function __construct(UserInterface $user, Authorizer $authorizer)
    {
        $this->user = $user;
        $this->authorizer = $authorizer;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->headers->has('Authorization')) {
            $this->authorizer->setRequest($request);
            if ($this->authorizer->validateAccessToken(false)) {
                $request->setUserResolver(function () {
                    return $this->user->byId($this->authorizer->getResourceOwnerId());
                });
                return $next($request);
            } else {
                abort(401, 'Invalid Access Credentials');
            }
        }
        abort(401, 'Authentication Missing');
        $next($request);
    }
}
