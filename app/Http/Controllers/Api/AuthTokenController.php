<?php

namespace WA\Http\Controllers\Api;

use LucaDegasperi\OAuth2Server\Authorizer;
use Illuminate\Routing\ResponseFactory as Response;
use WA\Auth\Auth;
use Auth as LaravelAuth;

use Cache;
use Illuminate\Database\Eloquent\Model as Model;

//use Illuminate\Contracts\Auth\UserProvider;

/**
 * Class AuthTokenController
 *
 * @package WA\Http\Controllers\Api
 */
class AuthTokenController extends ApiController
{
    //const AUTH_MANAGER = app()['auth'];
    //protected $provider;

    /*  
    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
    }
    */
    

    public function accessToken(Authorizer $authorizer, Response $response)
    {
        return $response->json($authorizer->issueAccessToken());
    }

    public function verify($email, $password)
    {
        $provider = $this->createUserProvider($config['provider']);
        $guard = new SessionGuard($name, $provider, $this->app['session.store']);

        $eloqUP = app()->make('Illuminate\Auth\EloquentUserProvider', ['algo']);
        //$eloqUP = new EloquentUserProvider();

        die;
        $user = $this->provider->retrieveByCredentials($credentials);  
        if($this->provider->validateCredentials($user, $credentials)){
        //if(true){
            echo 'ok!';
            //return $auth->user()->id;
        } else {
            echo 'no!';
            //return false;
        }











        /*
        $user = $this->user->where('username', strtolower($username))->first();

        if (app('hash')->check($password, $user->getAuthPassword())) {
            return $user->getKey();
        }

        return false;
        */
        




        /*
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];
        //var_dump('verify');
        //die;

        /*
        $authManager = app()['auth'];

        if ($authManager->once([
                "email" => $username,
                "password" => $password
            ])) 
        {
            return $authManager->user()->id;
        } else {
            return false;
        }
        */
        /* NOT WORKS!
        $auth = new Auth();

        if ($auth->login($credentials)) {
            return $auth->user()->id;
        } else {
            return false;
        }
        */
    }

    public function verifySSO($uuid)
    {
        $laravelUser = Cache::get('saml2user_'.$uuid);

        // WORKS!
        if (!isset($laravelUser)) {
            return false;
        } else {
            return $laravelUser['attributes']['id'];
        }
        

        /** 
         *  @TODOSAML2: We need to log the user with authentication 
         *  but session.store give us an error.
         *  Needs to be solved.
         */

        /*
        $authManager = app()['auth'];
        $credentials = [
            'id' => $laravelUser['id'],
            'uuid' => $laravelUser['uuid']
        ];

        if ($authManager->validate($credentials)) 
        {
            return $authManager->user()->id;
        } else {
            return false;
        }
        */
        /* NOT WORKS!
        // @TODO: @TODOSAML2: LaravelAuth::login($laravelUser)???
        if (LaravelAuth::loginUsingId($laravelUser['attributes']['id'])) {
            return $laravelUser['attributes']['id'];
        } else {
            return false;
        }
        */
    }
}

/*
{
    "id":1,
    "uuid":"29c33ece-11a1-3731-90d4-e9cfdf4e1fe6",
    "identification":"WA-57ac2f6263f90",
    "email":"dariana.donnelly@example.com",
    "alternateEmail":null,
    "username":"dariana.donnelly",
    "confirmation_code":"99ee7f3672d42ea6a49457737f1ca66e",
    "confirmed":1,
    "firstName":"Andre",
    "lastName":"Hills",
    "alternateFirstName":null,
    "supervisorEmail":"joy70@example.com",
    "companyUserIdentifier":null,
    "isSupervisor":0,
    "isValidator":0,
    "isActive":1,
    "rgt":null,
    "lft":null,
    "hierarchy":null,
    "defaultLang":"en",
    "notes":null,
    "level":0,
    "notify":0,
    "companyId":56,
    "syncId":null,
    "supervisorId":1,
    "externalId":null,
    "approverId":1,
    "defaultLocationId":251,
    "deleted_at":null,
    "created_at":"2016-08-11 07:55:17",
    "updated_at":"2016-08-11 07:55:17"
}
*/
