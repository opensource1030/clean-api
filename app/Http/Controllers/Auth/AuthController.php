<?php

namespace WA\Http\Controllers\Auth;

use LucaDegasperi\OAuth2Server\Authorizer;
use WA\Repositories\User\UserInterface;

use Cache;

/**
 * Class AuthController.
 */
class AuthController
{

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * AuthTokenController constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;

    }

    /**
     * @param Authorizer $authorizer
     * @return \Illuminate\Http\JsonResponse
     */
    public function accessToken(Authorizer $authorizer)
    {
        return response()->json($authorizer->issueAccessToken());
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function passwordGrantVerify($username, $password)
    {
        $user = $this->user->byEmail($username);

        if (app()['hash']->check($password, $user->getAuthPassword())) {
            return $user->getKey();
        }

        return false;
    }

    public function SSOGrantVerify($uuid)
    {
        $laravelUser = Cache::get('saml2user_'.$uuid);

        if (!isset($laravelUser)) {
            return false;
        } else {
            return $laravelUser['attributes']['id'];
        }
    }
}