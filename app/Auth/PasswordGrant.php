<?php

namespace WA\Auth;

use League\OAuth2\Server\Grant\PasswordGrant as PassGrant;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Event;
use League\OAuth2\Server\Exception;
use League\OAuth2\Server\Util\SecureKey;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

use DB;
use WA\DataStore\User\User;

/**
 * Password grant class.
 */
class PasswordGrant extends PassGrant
{
        /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        $user = $this->validateUser($request, $client);

        if (!$this->thisUserHasTheCorrectScope($scopes, $user->getIdentifier())) {
            $error['errors']['scopes'] = 'The User has not assigned the scope needed to complete the request.';
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    private function thisUserHasTheCorrectScope($scope, $userId){

        return true;

        // ROLES of the USER Retrieved From DB.
        $user = User::find($userId);
        $roles = $user->roles;        
        
        $perms = array();
        foreach ($scope as $scp) {
            // SCOPES requested by Name
            $var = Scope::findByName($scp);
            // PERMISSIONS of the Scope
            $varPerms = $var->permissions;
            // PUSH the Name of the Permissions into the Array
            array_push($perms, $varPerms);
        }

        // CKECK IF THE ROLES HAS THE PERMISSIONS
        if($user->ability($roles, $perms)){
            return true;
        }
        return false;
    }
}
