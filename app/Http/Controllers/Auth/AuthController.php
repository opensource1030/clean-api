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
        try {
            return response()->json($authorizer->issueAccessToken());

        } catch (\Exception $e){
            var_dump("ACCESSTOKEN Exception");
            var_dump($e);
            $error['errors']['errorType'] = $e->errorType;
            $error['errors']['message'] = $this->getErrorAndParse($e);
            return response()->json($error)->setStatusCode(401);
        }

    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function passwordGrantVerify($username, $password)
    {
        $user = $this->user->byEmail($username);

        if ($user && app()['hash']->check($password, $user->getAuthPassword())) {
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

    /*
     *      Transforms an Exception Object and gets the value of the Error Message.
     *
     *      @param:
     *          \Exception $e
     *      @return:
     *          $error->getValue($e);
     */
    private function getErrorAndParse($error){
        try{
            $reflectorResponse = new \ReflectionClass($error);
            $classResponse = $reflectorResponse->getProperty('message');
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($error);
            return $dataResponse;
        } catch (\Exception $e){
            return 'Generic Error';
        }
    }

}
