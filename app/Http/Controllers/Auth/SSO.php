<?php

/**
 * SSO - Controller of Single Sign On
 *  
 * @author   Agustí Dosaiguas
 */

namespace WA\Http\Controllers\Auth;

use Cache;
use Saml2;
use Validator;

use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request as Request;

use WA\Http\Controllers\ApiController;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\User\UserInterface;

/**
 * Class SSO.
 */
class SSO extends ApiController
{
    // Call the Interface to get the info of DB.
    protected $company;
    protected $user;

    // Lumen Request needed by Session.
    protected $request;

    public function __construct(CompanyInterface $company, UserInterface $user, Request $request)
    {
        $this->company = $company;
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * Login Request Function.
     *
     * @return response()->json
     */
    public function loginRequest($email) 
    {
        // email.
        $email = trim($email);

        $emailArray['email'] = $email;
        $validator = Validator::make($emailArray, [ 
            'email' => 'required|email',
        ]);

        // IF VALIDATOR OK => EMAIL OK!
        if(!$validator->fails()){
            // THIS EMAIL HAS COMPANY RELATED?
            $idCompany = $this->company->getIdByUserEmail($email);

            // SSO COMPANY == NULL -> LOOK DATABASE USER.        
            if($idCompany == 0){

                // LOOK DATABASE USER
                $user = $this->user->byEmail($email);
                
                // EMPLOYEE == NULL
                if($user == null){
                    //echo 'REGISTER USER OPTION';
                    //URL : http://clean.local/api/doSSO/dev@algo.com

                    return response()
                        ->json(['error' => 'User Not Found, Register Required', 'message' => 'Please, register a new user.'])
                        ->setStatusCode($this->status_codes['conflict']);

                // EMPLOYEE != NULL
                } else {
                    //echo 'ENTER PASSWORD OPTION';
                    // URL : http://clean.local/api/doSSO/dariana.donnelly@example.com

                    return response()
                        ->json(['error' => 'User Found, Password Required', 'message' => 'Please, enter your password.'])
                        ->setStatusCode($this->status_codes['conflict']);
                }

            // SSO COMPANY == NUMBER -> CONTINUE DOSSO
            } else {
                //echo 'SINGLE SIGN ON OPTION';
                // Microsoft : http://clean.local/api/doSSO/dev@wirelessanalytics.com
                // Facebook : http://clean.local/api/doSSO/dev@sharkninja.com

                $urlArray['url'] = app('request')->get('redirectToUrl');
                $validator = Validator::make($urlArray, [ 
                    'url' => 'required|url'
                ]);

                if($validator->fails()){
                    return response()
                        ->json(['error' => 'URL Not Found', 'message' => 'Url to redirect not found.'])
                        ->setStatusCode($this->status_codes['conflict']);
                } else {
                    Cache::put('saml2_idcompany_'.$email, $idCompany, 15);
                    $uuid = Uuid::generate();
                    
                    $redirectUrl = Saml2::login($urlArray['url'].'/'.$uuid);
                    $arrRU = array('redirectUrl' => $redirectUrl);
                    $arrD = array('data' => $arrRU);
                    return response()->json($arrD);
                }
            }
        // IF VALIDATOR FAILS => Error Response!
        } else {
            //echo 'INVALID EMAIL OPTION';
            // URL : http://clean.local/api/doSSO/dev

            return response()
                ->json(['error' => 'Invalid Email', 'message' => 'Please, enter a valid Email Address.'])
                ->setStatusCode($this->status_codes['conflict']);
        }        
    }

    /**
     * Login User Function.
     *
     * @return response()->json
     */
    public function loginUser($uuid) 
    {
        $laravelUser = Cache::get('saml2user_'.$uuid);
        if (!isset($laravelUser)) {
            return response()
                ->json(['error' => 'Required User', 'message' => 'Please, user is not available now, try again later.'])
                ->setStatusCode($this->status_codes['conflict']);
        } else {
            //echo ('SUCCESS UUID = '.$uuid);
            //echo ('<br>');
            //echo ('Laravel User: ');
            return response()
                ->json(['success' => 'User Successfully Logged', 'uuid' => $uuid])
                ->setStatusCode($this->status_codes['conflict']);
        }
    }
}