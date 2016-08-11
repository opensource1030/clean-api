<?php

/**
 * SSO - Controller of Single Sign On
 *  
 * @author   Agustí Dosaiguas
 */

namespace WA\Http\Controllers\Auth;
//use WA\Repositories\Company;

use WA\Http\Controllers\BaseController;
use URL;
use Saml2;
use Log;
use Auth;
use WA\User;
use Cache;
use Webpatser\Uuid\Uuid;
use WA\Auth\AuthInterface;
use Config;
use Illuminate\Routing\Redirector as Redirect;

// Call the Interface to get the info of DB.
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\User\UserInterface;

// LOGIN use! (Error)
use WA\Http\Controllers\Auth\AuthController;
use WA\Services\Form\Login\LoginForm;
use Illuminate\Http\Request as Request;

use Validator;

use Event;

/**
 * Class SSO.
 */
class SSO extends BaseController
{
    /**
     * @var \WA\Auth\AuthInterface
     */
    protected $auth;
    
    // Call the Interface to get the info of DB.
    protected $company;
    protected $user;
    protected $loginForm;    

    // Lumen Request needed by Session.
    protected $request;

    public function __construct(CompanyInterface $company, UserInterface $user, LoginForm $loginForm, Request $request)
    {
        $this->loginForm = $loginForm;
        $this->company = $company;
        $this->user = $user;
        $this->request = $request;
    }

    public function loginRequest($email) 
    {
        // Save email in Session to show it to the user.
        $email = trim($email);

        $emailArray['email'] = $email;
        $validator = Validator::make($emailArray, [ 
            'email' => 'required|email',
        ]);

        // IF VALIDATOR OK => EMAIL OK!
        if(!$validator->fails()){
            // THIS EMAIL HAS COMPANY RELATED?
            $companyId = $this->company->getIdByUserEmail($email);

            // SSO COMPANY == NULL -> LOOK DATABASE USER.        
            if($companyId == 0){

                // LOOK DATABASE USER
                $user = $this->user->byEmail($email);
                
                // EMPLOYEE == NULL
                if($user == null){
                    // Register User Option
                    //URL : http://clean.local/doSSO/dev@algo.com

                    response()
                        ->json(['error' => 'Required Register', 'message' => 'Please, register a new user.'])
                        ->setCallback($this->request->input($email));

                // EMPLOYEE != NULL
                } else {
                    // Enter Password Option
                    // URL : http://clean.local/doSSO/dariana.donnelly@example.com

                    response()
                        ->json(['error' => 'Required Password', 'message' => 'Please, enter your password.'])
                        ->setCallback($this->request->input($email));
                }

            // SSO COMPANY == NUMBER -> CONTINUE DOSSO
            } else {
                // Single Sign On Option
                // Microsoft : http://clean.local/doSSO/dev@wirelessanalytics.com
                // Facebook : http://clean.local/doSSO/dev@sharkninja.com

                // @TODOSAML2: Call to undefined method Redis::connection() ERROR.

                Cache::put('saml2_idcompany_'.$email, $companyId, 15);
                //var_dump('saml2_idcompany: '.Cache::get('saml2_idcompany_'.$email));
                $uuid = Uuid::generate();
                Saml2::login("/doSSO/login/".$uuid);
            }
        // IF VALIDATOR FAILS => Error Response!
        } else {
            // Invalid Email Option
            // URL : http://clean.local/doSSO/dev

            response()
                ->json(['error' => 'Required Email', 'message' => 'Please, enter a valid Email Address.'])
                ->setCallback($this->request->input($email));
        }        
    }

    public function loginUser($uuid) 
    {
        $laravelUser = Cache::get('saml2user_'.$uuid);
        if (!isset($laravelUser)) {
            echo (' ERROR: The user login is not available now, please try again later');
            response()
                ->json(['error' => 'Required User', 'message' => 'Please, user is not available now, try again later.'])
                ->setCallback($this->request->input($email));
        } else {
            echo ('SUCCESS UUID = '.$uuid);
            echo ('<br>');
            echo ('Laravel User: ');
            var_dump($laravelUser);
        }
    }
}