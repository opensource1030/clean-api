<?php

/**
 * MainHandler - Gets the event received by the Single Sign On.
 *  
 * @author   Agustí Dosaiguas
 */

namespace WA\Events\Handlers\Saml2;

use Illuminate\Contracts\Events\Dispatcher;
use WA\DataStore\CarrierDestinationMap;
use WA\Events\Handlers\BaseHandler;

use Auth;
use WA\Events\PodcastWasPurchased;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;

use WA\DataStore\User\User;

use Cache;

use Carbon\Carbon;
use WA\Services\Form\User\UserForm;

/**
 * Class MainHandler.
 */
class MainHandler extends BaseHandler
{
    protected $userForm;

    protected $userEmail = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
    protected $userLastName = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
    protected $userFirstName = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';

    /**
     * @param ProcessLogRepositoryInterface    $processLog
     * @param DumpExceptionRepositoryInterface $dumpExceptions
     */

    public function __construct(UserForm $userForm) {
        $this->userForm = $userForm;
    }
    

    /**
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function saml2LoginUser($event)
    {
        // Get the UUID from url.
        $uuid = $this->getUuidFromRequestRelayState();
        
        // Get the User Data Info from the Saml2 User.
        $userData = $this->getUserDataFromSaml2User($event);

        // Id Company from Request.
        $idCompany = app('request')->get('idCompany');

        // Get IDP user Email from User Data Info
        $email = $this->getEmailFromUserData($userData, $idCompany);

        if (isset($email)) {
            $laravelUser = User::where('email',$email) -> first();
            if (!isset($laravelUser)) {
                $user = $this->parseRequestedInfoFromIdp($userData, $idCompany);
                // @TODOSAML2: Call to undefined method Redis::connection() ERROR.
                $this->createUserSSO($user);
            }
            Cache::put('saml2user_'.$uuid, $laravelUser, 15);
        }
        return true;
    }

    /**
     * @param Dispatcher $events
     */
    public function handle(Dispatcher $events)
    {
        $events->listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', 'WA\Events\Handlers\Saml2\MainHandler@saml2LoginUser');
    }

    private function parseRequestedInfoFromIdp($userData, $idCompany){

        // The today's date.
        $carbon = Carbon::today();

        switch ($idCompany) {
            case 21: // facebook
                return $this->createUserFacebookTest();
                //return null;                
                break;
            
            default: // microsoft
                $user = array(
                    'email' => $userData['attributes'][$this->userEmail][0],
                    'alternateEmail' => '',
                    'password' => '1@6~%&',
                    'username' => explode('@',$userData['attributes'][$this->userEmail][0])[0],
                    'confirmation_code' => '',
                    'remember_token' => NULL,
                    'confirmed' => 1,
                    'firstName' => $userData['attributes'][$this->userFirstName][0],
                    'alternateFirstName' => NULL,
                    'lastName' => $userData['attributes'][$this->userLastName][0],
                    'supervisorEmail' => $userData['attributes'][$this->userEmail][0],
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

    private function createUserFacebookTest(){

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

    private function getUuidFromRequestRelayState(){
        $relayState = app('request')->input('RelayState');
        $path_parts = explode('/', $relayState);
        return $path_parts[count($path_parts)-1];
    }

    private function getUserDataFromSaml2User($event){
        // Get Saml2 User from $Event.
        $user = $event->getSaml2User();

        // Get the User Data Info from the Saml2 User.
        return [
        'id' => $user->getUserId(),
        'attributes' => $user->getAttributes(),
        'assertion' => $user->getRawSamlAssertion()
        ];
    }

    private function getEmailFromUserData($userData, $idCompany){

        // FACEBOOK VERSION
        if($idCompany == 21){
            return "dariana.donnelly@example.com";
            //return $userData['attributes']['facebook_user'][0];
        }

        // DEFAULT VERSION (MICROSOFT)
        return $userData['attributes'][$this->userEmail][0];
    }
}
