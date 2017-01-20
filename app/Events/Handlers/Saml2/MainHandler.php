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

/**
 * Class MainHandler.
 */
class MainHandler extends BaseHandler
{
    protected $userEmail = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
    protected $userLastName = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
    protected $userFirstName = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';

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
            $laravelUser = User::where('email', $email)->first();
            if (!isset($laravelUser)) {
                // CREATE USER
                $infoUser = $this->parseRequestedInfoFromIdp($userData, $idCompany);
                $userInterface = app()->make('WA\Repositories\User\UserInterface');
                $laravelUser = $userInterface->create(array('email' => $infoUser['email'], 'companyId' => $idCompany));
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

    private function parseRequestedInfoFromIdp($userData, $idCompany)
    {
        //return array('email' => 'dev@testing.com'); // TESTING

        switch ($idCompany) {
            default: // MICROSOFT
                $user = array(
                    'email' => $userData['attributes'][$this->userEmail][0],
                    'firstName' => $userData['attributes'][$this->userFirstName][0],
                    'lastName' => $userData['attributes'][$this->userLastName][0],
                    'companyId' => $idCompany,
                    );

                return $user;
        }
    }

    private function getUuidFromRequestRelayState()
    {
        $relayState = app('request')->input('RelayState');
        $path_parts = explode('/', $relayState);

        return $path_parts[count($path_parts) - 1];
    }

    private function getUserDataFromSaml2User($event)
    {
        // Get Saml2 User from $Event.
        $user = $event->getSaml2User();

        // Get the User Data Info from the Saml2 User.
        return [
            'id' => $user->getUserId(),
            'attributes' => $user->getAttributes(),
            'assertion' => $user->getRawSamlAssertion()
        ];
    }

    private function getEmailFromUserData($userData, $idCompany)
    {
        //if (isset($userData['attributes'][$this->userEmail][0])) { // TESTING
            return $userData['attributes'][$this->userEmail][0];
        //}
        //return 'dev@testing.com';
    }
}
