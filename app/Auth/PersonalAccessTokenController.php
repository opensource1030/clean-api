<?php

namespace WA\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenResult;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use WA\DataStore\User\User;
use Auth;
use Illuminate\Validation\ValidationException;

class PersonalAccessTokenController extends \Laravel\Passport\Http\Controllers\PersonalAccessTokenController
{
    /**
     * Create a new personal access token for the user.
     *
     * @param  Request  $request
     * @return PersonalAccessTokenResult
     */
    public function store(Request $request)
    {
        try {
            $userScopes = $this->retrieveScopesRelatedToTheUserRole();
            $this->validation->make($request->all(), [
                'name' => 'required|max:255',
                'scopes' => 'array|in:'.implode(',', $userScopes),
            ])->validate();    
        } catch (ValidationException $ve) {
            $error['errors']['validation'] = 'Validation Exception';
            $error['errors']['userScopes'] = $userScopes;
            $error['errors']['requestScopes'] = $request->all()['scopes'];
            return response()->json($error)->setStatusCode(422);
        }        

        return $request->user()->createToken(
            $request->name, $request->scopes ?: []
        );
    }

    private function retrieveScopesRelatedToTheUserRole() {
        $user = Auth::user();
        $roles = $user->roles;
        
        $scopeList = [];
        foreach ($roles as $role) {
            $permissions = $role->permissions;
            foreach ($permissions as $perm) {
                $scopes = $perm->scopes;
                foreach ($scopes as $spc) {
                    array_push($scopeList, $spc->name);
                }
            }
        }

        $scopeList = array_unique($scopeList);

        return $scopeList;
    }
}
