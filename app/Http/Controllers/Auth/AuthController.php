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
        
        } catch (UnsupportedGrantTypeException $ugte) {
            // GRANT_TŶPE (Invalid)
            // GRANT_TYPE (Not Found)
            // ERROR 400

            $error['errors']['errorType'] = $ugte->errorType;
            $error['errors']['parameter'] = $ugte->parameter;
            $error['errors']['message'] = $this->getErrorAndParse($ugte);
            return response()->json($error)->setStatusCode(400);

        } catch (InvalidRequestException $ire) {
            // CLIENT_ID (Not Found)
            // CLIENT_SECRET (Not Found)
            // ERROR 400

            $error['errors']['errorType'] = $ire->errorType;
            $error['errors']['parameter'] = $ire->parameter;
            $error['errors']['message'] = $this->getErrorAndParse($ire);
            return response()->json($error)->setStatusCode(400);

        } catch (InvalidClientException $ice) {
            // CLIENT_ID (Invalid)
            // CLIENT_SECRET (Invalid)
            // ERROR 401

            $error['errors']['errorType'] = $ice->errorType;
            $error['errors']['message'] = $this->getErrorAndParse($ice);
            return response()->json($error)->setStatusCode(401);

        } catch (InvalidCredentialsException $icre) {
            // PASSWORD (No Valid)
            // ERROR 401

            $error['errors']['errorType'] = $icre->errorType;
            $error['errors']['message'] = $this->getErrorAndParse($icre);
            return response()->json($error)->setStatusCode(401);

        } catch (\Exception $e){
            // ERROR 500

            if(isset($e->errorType) && $e->errorType <> null) {
                $error['errors']['errorType'] = $e->errorType;    
            } else {
                $error['errors']['errorType'] = "Unknown Error";
            }

            if(isset($e->httpStatusCode) && $e->httpStatusCode <> null) {
                $httpStatusCode = $e->httpStatusCode;    
            } else {
                $httpStatusCode = 500;
            }


            $error['errors']['message'] = $this->getErrorAndParse($e);
            return response()->json($error)->setStatusCode($httpStatusCode);
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
}