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

use Session;
use Validator;

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

    public function __construct(CompanyInterface $company, UserInterface $user, LoginForm $loginForm)
    {
        $this->loginForm = $loginForm;
        $this->company = $company;
        $this->employee = $user;
    }

    public function loginRequest($email) 
    {
        // Save email in Session to show it to the user.
        $email = trim($email);
        Session::set('email', $email);

        Log::info("LOGIN REQUEST: ".print_r($email, true));

        $emailArray['email'] = $email;
        $validator = Validator::make($emailArray, [ 
            'email' => 'required|email',
        ]);

        // IF VALIDATOR OK => EMAIL OK!
        if(!$validator->fails()){
            
            // THIS EMAIL HAS COMPANY RELATED?
            $companyId = $this->company->getIdByUserEmail($email);
            
            Log::info("COMPANY ID: ".print_r($companyId, true));
            // SSO COMPANY == NULL -> LOOK DATABASE USER.        
            if($companyId == null){

                // LOOK DATABASE USER
                $user = $this->employee->byEmail($email);
                //Log::info("EMPLOYEE: ".print_r($user, true));
                // EMPLOYEE == NULL
                if($user == null){
                    //SAVE OPTION EXISTS ( NO => REGISTER )
                    Log::info("LOGIN OK - REGISTER!");
                    $this->loginForm->notify('info', trans('Your user does not exist, please Register a new one.'));
                    Session::set('employee_exists', 'no');

                // EMPLOYEE != NULL
                } else {
                    //SAVE OPTION EXISTS ( YES => LOGIN )
                    Log::info("LOGIN OK - PASSWORD!");
                    $this->loginForm->notify('info', trans('Please, enter your password.'));
                    Session::set('employee_exists', 'yes');
                }

                // REDIRECT TO LOGIN.                
                return redirect('/login');

            // SSO COMPANY == NUMBER -> CONTINUE DOSSO
            } else {
                Log::info("LOGIN OK - DOSSO!");
                Session::set('saml2_idcompany', $companyId);
                $uuid = Uuid::generate();
                Saml2::login("/doSSO/login/".$uuid);
            }
        // IF VALIDATOR FAILS => Redirect to login with error!
        } else {
            Log::info("LOGIN FAILS - NO VALID!");
            $this->loginForm->notify('error', trans('Please, input a valid email'));
            return redirect('/login');
        }        
    }

    public function loginUser($uuid) 
    {                
        $laravelUser = Cache::get('saml2user_'.$uuid);
        if (!isset($laravelUser)) {
            //Log::info("NO LARAVEL USER!");
            $this->loginForm->notify('error', trans('The user cached is not available now, please try again later'));
            return redirect(config('saml2_settings.errorRoute'));  
        }
        //Log::info("LARAVEL USER!");
        Auth::login($laravelUser);
        return redirect(config('saml2_settings.loginRoute'));  
    }
}