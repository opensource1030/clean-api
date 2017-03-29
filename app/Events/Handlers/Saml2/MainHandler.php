<?php

/**
 * MainHandler - Gets the event received by the Single Sign On.
 *
 * @author   Agustí Dosaiguas
 */

namespace WA\Events\Handlers\Saml2;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use WA\DataStore\CarrierDestinationMap;
use WA\DataStore\Company\CompanySaml2;
use WA\DataStore\User\User;

use WA\Events\Handlers\BaseHandler;
use WA\Events\PodcastWasPurchased;

use Auth;
use Cache;
use Carbon\Carbon;
use Log;

/**
 * Class MainHandler.
 */
class MainHandler extends BaseHandler
{
    protected $userEmail = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress';
    protected $userGivenName = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';
    protected $userName = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
    protected $userUPN = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn';
    protected $userSurname = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
    protected $userPersonalIdentifier = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/privatepersonalidentifier';
    protected $userNameIdentifier = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier';
    protected $userDenyonlysid = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/denyonlysid';
    protected $userRSA = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/rsa';
    protected $userThumb = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/thumbprint';

    /**
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function saml2LoginUser($event)
    {
        // Get the User Data Info from the Saml2 User.
        $userData = $this->getUserDataFromSaml2User($event);
        Log::debug("MainHandler@saml2LoginUser - userData: " . print_r(json_encode($userData), true));

        $samlResponse = base64_decode(app('request')->get('SAMLResponse'));
        $xml = new \SimpleXMLElement($samlResponse);                
        $entityIdNode = $xml->xpath("/*[local-name()='Response']/*[local-name()='Issuer']");
        $entityId = $entityIdNode[0]->__toString();
        
        $companySaml = CompanySaml2::where('entityId', $entityId)->first();
        $idCompany = $companySaml['companyId'];

        $infoUser = $this->parseRequestedInfoFromIdp($userData, $idCompany);
        Log::debug("MainHandler@saml2LoginUser - infoUser: " . print_r($infoUser, true));

        // Get IDP user Email from User Data Info
        if (isset($infoUser['email'])) {
            $laravelUser = User::where('email', $infoUser['email'])->first();
            if (!isset($laravelUser)) {

                // CREATE USER
                $userInterface = app()->make('WA\Repositories\User\UserInterface');
                $laravelUser = $userInterface->create(array('email' => $infoUser['email'], 'companyId' => $idCompany));
            }

            // Get the UUID from url.
            $uuid = $this->getUuidFromRequestRelayState();
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
        //return array('email' => 'dev@testsaml2.com', 'firstName' => 'Sirion', 'lastName' => 'Developers', 'isActive' => 1); // TESTING

        switch ($idCompany) {
            default: // MICROSOFT
                $user = array(
                    'email' => isset($userData['attributes'][$this->userEmail][0]) ? $userData['attributes'][$this->userEmail][0] : '',
                    'firstName' => isset($userData['attributes'][$this->userName][0]) ? $userData['attributes'][$this->userName][0] : '',
                    'companyId' => $idCompany,
                    'isActive' => 1,
                    'givenName' => isset($userData['attributes'][$this->userGivenName][0]) ? $userData['attributes'][$this->userGivenName][0] : '',
                    'upn' => isset($userData['attributes'][$this->userUPN][0]) ? $userData['attributes'][$this->userUPN][0] : '',
                    'surname' => isset($userData['attributes'][$this->userSurname][0]) ? $userData['attributes'][$this->userSurname][0] : '',
                    'personalIdentifier' => isset($userData['attributes'][$this->userPersonalIdentifier][0]) ? $userData['attributes'][$this->userPersonalIdentifier][0] : '',
                    'nameIdentifier' => isset($userData['attributes'][$this->userNameIdentifier][0]) ? $userData['attributes'][$this->userNameIdentifier][0] : '',
                    'denyonlysid' => isset($userData['attributes'][$this->userDenyonlysid][0]) ? $userData['attributes'][$this->userDenyonlysid][0] : '',
                    'rsa' => isset($userData['attributes'][$this->userRSA][0]) ? $userData['attributes'][$this->userRSA][0] : '',
                    'thumb' => isset($userData['attributes'][$this->userThumb][0]) ? $userData['attributes'][$this->userThumb][0] : '',
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
}
