<?php

/**
 * MainHandler - Gets the event received by the Single Sign On.
 *  
 * @author   Agustí Dosaiguas
 */

namespace WA\Events\Handlers\Saml2;

use Illuminate\Events\Dispatcher;
use WA\DataStore\CarrierDestinationMap;
use WA\Events\Handlers\BaseHandler;
use WA\Repositories\DumpExceptionRepositoryInterface;
use WA\Repositories\ProcessLogRepositoryInterface;

use Log;
use Auth;
use WA\Events\PodcastWasPurchased;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;

use WA\DataStore\User\User;

use Cache;
use Session;

use Carbon\Carbon;
use WA\Services\Form\User\UserForm;
/**
 * Class MainHandler.
 */
class MainHandler extends BaseHandler
{
    protected $dumpExceptions;
    protected $processLog;
    protected $userForm;

    private $USER_EMAIL = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
    private $USER_LASTNAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
    private $USER_FIRSTNAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';

    /**
     * @param ProcessLogRepositoryInterface    $processLog
     * @param DumpExceptionRepositoryInterface $dumpExceptions
     */
    public function __construct(
        UserForm $userForm,
        ProcessLogRepositoryInterface $processLog,
        DumpExceptionRepositoryInterface $dumpExceptions
        ) {
        $this->userForm = $userForm;
        $this->processLog = $processLog;
        $this->dumpExceptions = $dumpExceptions;
    }

    /**
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function saml2LoginUser($event)
    {
        // Get the UUID from url.
        $relayState = app('request')->input('RelayState');
        $parts = parse_url($relayState);
        $path_parts = explode('/', $parts['path']);
        $uuid = $path_parts[count($path_parts)-1]; 
        
        

        // Get Saml2 User from $Event.
        $user = $event->getSaml2User();

        // Get the User Data Info from the Saml2 User.
        $userData = [
        'id' => $user->getUserId(),
        'attributes' => $user->getAttributes(),
        'assertion' => $user->getRawSamlAssertion()
        ];

        // Id Company from Request.
        $idCompany = app('request')->get('idCompany');

        //Log::info("USERDATA: ".print_r($userData, true));

        // Get IDP user Email from User Data Info
        $email = $this->getEmailFromUserData($userData, $idCompany);

        if (isset($email)) {
            $laravelUser = User::where('email',$email) -> first();
            if (!isset($laravelUser)) {
                $user = $this->parseRequestedInfoFromIdp($userData, $idCompany);
                //Log::info("USER WA/Events/Handlers/Saml2/MainHandler: ".print_r($user, true));
                $this->createUserSSO($user);
            }
            Cache::put('saml2user_'.$uuid, $laravelUser, 1);
        }
        return true;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', 'WA\Events\Handlers\Saml2\MainHandler@saml2LoginUser');
    }

    private function parseRequestedInfoFromIdp($userData, $idCompany){

        // Generate Random Password
        $helper = app()->make('WA\Http\Controllers\Admin\HelperController');

        // The today's date.
        $carbon = Carbon::today();

        switch ($idCompany) {
            case 21:
                return $this->createUserFacebookTest($userData);
                //return null;                
                break;
            
            default:
                $user = array(
                    'email' => $userData['attributes'][$this->USER_EMAIL][0],
                    'alternateEmail' => '',
                    'password' => $helper->randGenerator('', 7, ''),
                    'username' => explode('@',$userData['attributes'][$this->USER_EMAIL][0])[0],
                    'confirmation_code' => '',
                    'remember_token' => NULL,
                    'confirmed' => 1,
                    'firstName' => $userData['attributes'][$this->USER_FIRSTNAME][0],
                    'alternateFirstName' => NULL,
                    'lastName' => $userData['attributes'][$this->USER_LASTNAME][0],
                    'supervisorEmail' => $userData['attributes'][$this->USER_EMAIL][0],
                    'companyUserIdentifier' => '',
                    'isSupervisor' => 0,
                    'isValidator' => 0,
                    'isActive' => 0,
                    'rgt ' => NULL,
                    'lft ' => NULL,
                    'hierarchy' => NULL,
                    'notes' => '',
                    'companyId' => $idCompany,
                    'syncId' => NULL,
                    'supervisorId' => NULL,
                    'externalId' => NULL,
                    'approverId' => NULL,
                    'deleted_at' => NULL,
                    'created_at' => $carbon->format('Y-m-d H:i:s'),
                    'updated_at' => NULL,
                    'defaultLocationId' => '',
                    'defaultLang' => 'en',
                    'departmentId' => NULL,
                    'identification' => '',
                    'notify' => 0,
                    'apiToken' => NULL,
                    'level' => 0
                    );
                return $user;
            break;
        }
    }

    private function createUserSSO($user){
        $data['email'] = $userInfo['email'] = $user['email'];
        //$data['alternateEmail'] = $user['alternateEmail']; // ADDED
        $data['password'] = $user['password']; //OK
        $data['username'] = $user['username']; //OK
        //$data['confirmation_code'] = $user['confirmation_code']; // ADDED
        //$data['remember_token'] = $user['remember_token']; // ADDED
        $data['confirmed'] = $user['confirmed']; //OK
        $data['firstName'] = $userInfo['firstName'] = $user['firstName'];
        $data['alternateFirstName'] = $user['alternateFirstName'];
        $data['lastName'] = $userInfo['lastName'] = $user['lastName'];
        $data['supervisorEmail'] = $user['supervisorEmail']; //OK
        $data['companyUserIdentifier'] = $userInfo['companyUserIdentifier'] = '';
        $data['isSupervisor'] = $user['isSupervisor']; //OK
        $data['isValidator'] = $user['isValidator']; //OK 
        //$data['isActive'] = $user['isActive']; // ADDED
        //$data['rgt'] = $user['rgt']; // ADDED
        //$data['lft'] = $user['lft']; // ADDED
        //$data['hierarchy'] = $user['hierarchy']; // ADDED
        $data['notes'] = $user['notes']; //OK
        $data['companyId'] = $user['companyId'];
        //$data['syncId'] = $user['syncId']; // ADDED
        $data['supervisorId'] = $user['supervisorId']; //OK
        //$data['externalId'] = $user['externalId']; // ADDED
        //$data['approverId'] = $user['approverId']; // ADDED
        //$data['deleted_at'] = $user['deleted_at']; // ADDED
        //$data['created_at'] = $user['created_at']; // ADDED
        //$data['updated_at'] = $user['updated_at']; // ADDED
        $data['defaultLocationId'] = $user['defaultLocationId']; //OK      
        $data['defaultLang'] = $user['defaultLang']; //OK
        $data['departmentId'] = $this->userForm->getDepartmentPathId([], null, $userInfo);; //OK
        //$data['identification'] = $user['identification']; // ADDED
        $data['notify'] = $user['notify']; //OK
        //$data['apiToken'] = $user['apiToken']; // ADDED
        $data['level'] = $user['level']; //OK        
        $data['companyExternalId'] = ''; //???
        $data['approverId'] = '';
        $data['password_confirmation'] = $user['password'];
        $data['isCensusCompany'] = 1;
        $data['udls'] = ['first', 'last'];
        $data['evDepartmentId'] = $this->userForm->getDepartmentPathId([], null, $userInfo, true);
        $data['user_roles'] = '';

        //var_dump($data);
        //die;

        // @TODO: TODOSAML2: This Function gives me an error. Waiting for news.
        if (!$this->userForm->create($data)) {
            $data['errors'] = $this->userForm->errors();
            return Redirect::back()
            ->withInput()
            ->withErrors($this->userForm->errors());
        }

        $data['employee'] = $this->userForm->getUserByEmail($data['email']);
        $userId = $data['employee']['id'];

        return redirect("users/$userId")->with($data);
    }

    private function getEmailFromUserData($userData, $idCompany){

        // FACEBOOK VERSION
        if($idCompany == 21){
            return $userData['attributes']['facebook_user'][0];
        }

        // DEFAULT VERSION (MICROSOFT)
        return $userData['attributes'][$this->USER_EMAIL][0];
    }

    private function createUserFacebookTest($userData){

        // The today's date.
        $carbon = Carbon::today();

        return array(
                    'email' => 'email@sharkninja.com',
                    'alternateEmail' => '',
                    'password' => '6%(3.D@',
                    'username' => 'pruebafacebook',
                    'confirmation_code' => '',
                    'remember_token' => NULL,
                    'confirmed' => 1,
                    'firstName' => 'prueba',
                    'alternateFirstName' => NULL,
                    'lastName' => 'facebook',
                    'supervisorEmail' => 'email@sharkninja.com',
                    'companyUserIdentifier' => '',
                    'isSupervisor' => 0,
                    'isValidator' => 0,
                    'isActive' => 0,
                    'rgt ' => NULL,
                    'lft ' => NULL,
                    'hierarchy' => NULL,
                    'notes' => '',
                    'companyId' => 21,
                    'syncId' => NULL,
                    'supervisorId' => NULL,
                    'externalId' => NULL,
                    'approverId' => NULL,
                    'deleted_at' => NULL,
                    'created_at' => $carbon->format('Y-m-d H:i:s'),
                    'updated_at' => NULL,
                    'defaultLocationId' => '',
                    'defaultLang' => 'en',
                    'departmentId' => NULL,
                    'identification' => '',
                    'notify' => 0,
                    'apiToken' => NULL,
                    'level' => 0
                    );
    }
}