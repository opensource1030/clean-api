<?php

namespace WA\Auth;

use Auth as IlluminateAuth;
use Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use WA\Repositories\Company\CompanyInterface;

/**
 * Class Auth.
 */
class Auth implements AuthInterface
{
    // Lumen Request needed.
    protected $request;
    protected $company;

    public function __construct(CompanyInterface $company, Request $request)
    {
        $this->request = $request;
        $this->company = $company;
    }

    /**
     * Attempts to login with the given credentials.
     *
     * @param array $input    Array containing the credentials (email/username and password)
     * @param bool  $remember
     *
     * @return bool Success?
     */
    public function login($input, $remember = false)
    {
        if (!isset($input['password'])) {
            $input[ 'password' ] = null;
        }

        $response = IlluminateAuth::attempt(['email' => $input['email'], 'password' => $input['password']], $remember);

        return $response;
    }

    /**
     * Attempts to logout an active employee
     **.
     *
     * @return bool Success?
     */
    public function logout()
    {
        return IlluminateAuth::logout();
    }

    /**
     * Checks if the credentials has been throttled by too
     * much failed login attempts.
     *
     * @param array $input Array containing the credentials (email/username and password)
     *
     * @return bool Is throttled
     */
    public function isThrottled($input)
    {
        return false;
    }

    /**
     * Checks if the given credentials corresponds to a employee that exists but
     * is not confirmed.
     *
     * @param array $input Array containing the credentials (email/username and password)
     *
     * @return bool Exists and is not confirmed?
     */
    public function existsButNotConfirmed($input)
    {
        return IlluminateAuth::attempt([
            'email' => $input['email'],
            'password' => $input['password'],
            'confirmed' => 1,
        ]);
    }

    /**
     * Resets a password of a employee. The $input['token'] will tell which employee.
     *
     * @param array $input Array containing 'token', 'password' and 'password_confirmation' keys
     *
     * @return bool Success
     */
    public function resetPassword($email)
    {
        $user = $this->findUserByEmail($email);

        $idCompany = $this->company->getIdByUserEmail($email);
        if($idCompany <= 0) {
            return 'Company Not Found';
        }

        $company = $this->company->byId($idCompany);

        if ($company->saml2Settings() != null) {
            return 'Company has SSO';
        }

        if ($user == null) {
            return 'User Null';
        }

        $code = bin2hex(random_bytes(64));

        $identification = $user->identification;

        $redirectPath = $identification.'/'.$code;

        $data = [
            'identification' => $identification,
            'redirectPath' => $redirectPath,
        ];

        $mail = Mail::send('emails.auth.password', $data, function ($m) use ($email, $user) {
            $m->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
            $m->to($email)->subject('Reset Password Requested by '.$user->username.' !');
        });

        Cache::put('user_email_'.$code, $identification, 60);
        Cache::put('user_code_'.$identification, $code, 60);

        return 'Ok';
    }

    private function findUserByEmail($email) {
        $apiName = 'WA\DataStore\User\User';
        $api = app()->make($apiName);
        return $api->findForPassport($email);
    }

    private function findUserByIdentification($identification) {
        $apiName = 'WA\DataStore\User\User';
        $api = app()->make($apiName);
        return $api->findForIdentification($identification);
    }

    public function getPasswordFromEmail($identification, $code) {
        $user = $this->findUserByIdentification($identification);

        if($this->request['password1'] == $this->request['password2']) {
            $identificationCache = Cache::get('user_email_'.$code);
            $codeCache = Cache::get('user_code_'.$identification);

            if($user != null) {
                if($code == $codeCache) {
                    if ($identification == $identificationCache) {

                        $data = [
                            'id' => $user->id,
                            'password' => $this->request['password1']
                        ];

                        $userInterface = app()->make('WA\Repositories\User\UserInterface');
                        $userUpdated = $userInterface->update($data);

                        Cache::forget('user_email_'.$code);
                        Cache::forget('user_code_'.$identification);

                        return 'Ok';
                    }
                }
            }
        }

        return 'Error';
    }

    /**
     * Validate if the user is logged in/or not.
     *
     * @return bool
     */
    public function loggedIn()
    {
        return (bool) IlluminateAuth::user();
    }

    /**
     * Get the User if exists.
     *
     * @return object of user
     */
    public function user()
    {
        return IlluminateAuth::user();
    }
}

