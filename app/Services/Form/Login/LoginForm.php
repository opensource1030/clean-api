<?php

namespace WA\Services\Form\Login;

use WA\Repositories\Allocation\AllocationInterface;
use WA\Auth\AuthInterface;
use WA\Services\Form\AbstractForm;
use WA\Services\Form\Dashboard\DashboardForm;
use WA\Services\Form\Exception\FormException;
use WA\Services\Validation\ValidableInterface;


/**
 * Class LoginForm.
 */
class LoginForm extends AbstractForm
{
    /**
     * Data from form.
     *
     * @var array
     */
    protected $input;

    /**
     * @var \WA\Services\Validation\ValidableInterface
     */
    protected $validator;

    /**
     * @var \WA\Auth\AuthInterface
     */
    protected $auth;


    protected $allocations;

    use ResetsPasswords;
    use ValidatesRequests;

    /**
     * @param AuthInterface      $auth
     * @param ValidableInterface $validator
     * @param AllocationInterface  $allocations
     */
    public function __construct(AuthInterface $auth, ValidableInterface $validator, AllocationInterface $allocations)
    {
        $this->validator = $validator;
        $this->auth = $auth;
        $this->allocations = $allocations;
    }

    /**
     * Log a user in`.
     *
     * @param array         $input
     * @param bool          $remember
     * @param DashboardForm $dashboard
     *
     * @return bool
     */
    public function login(array $input, $remember = false, DashboardForm $dashboard = null)
    {
        if (!$this->valid($input)) {
            $this->errors = $this->validator->errors();
            $this->notify('error', 'We there was some issues with the data, please verify');

            return false;
        }

        try {
            if (!($loggedIn = $this->auth->login($input, $remember))) {
                $msg = trans('users.alerts.wrong_credentials');
                $this->notify('error', $msg);

                return false;
            }

            $user = $this->auth->user();
            $dashboard_data =  ['companyId' => (int)$user->companyId];

            $dashboard = $dashboard ?: app()->make('WA\Services\Form\Dashboard\DashboardForm');
            $dashboard->updateCompanySession($dashboard_data);

            $this->notify('success', 'You are now logged in, Welcome');

            return $loggedIn;
        } catch (FormException $e) {
            $this->notify('error', 'There was a problem processing that request. Please try later');

            return false;
        }
    }

    /**
     * Log user out.
     *
     * @return bool
     */
    public function logout()
    {
        $this->notify('info', 'You are logged out, bye');

        $response = $this->auth->logout();

        return $response;
    }

    public function loggedIn()
    {
        return $this->auth->loggedIn();
    }

    /**
     * Reset User Password
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function resetUserPassword($request)
    {

        return $this->postReset($request);
    }

    /**
     * Send link to user email, to reset password
     *
     * @param $request
     * @return mixed
     */
    public function sendPasswordResetLink($request)
    {
        $isLegacy = (bool)$request->input('legacy');
     
        if($isLegacy)
        {
            Session::set('appName', 'CLEAN Platform');
        }else{
            Session::set('appName', 'Marsalis');
        }

        $request['email'] = trim($request['email'], " ");
        $response =  $this->postEmail($request);

        if($request->session()->has('status'))
        {
            $message = $request->session()->get('status');
            $this->notify('success', $message);
        }

        if($request->session()->has('errors'))
        {
            $message = "";
            $errors =   $request->session()->get('errors');
            foreach($errors->getMessages() as $err){
                $message = $err[0];
            }

            if(!empty($message)){
                $this->notify('error', 'Password Request Failed. The email address entered does not exist in the system. Please enter your work email address or create a new account.');
            }
        }


        return $response->withErrors('');
    }

    /**
     * Form to reset password
     *
     * @param null $token
     * @return mixed
     */
    public function resetRequest($token = null, $request)
    {
        $isLegacy = (bool)$request->input('legacy');
        $legacy_destination = $request->input('legacy_dest');
        // Get the email address with the token
        $token_info =  \WA\DataStore\User\PasswordResets::where('token', $token)->first();

        $email = !empty($token_info) ? $token_info->email : '';
        if(!$email)
        {
            $this->notify('error', 'Your password request token has expired. Please request password reset again.');
        }

        return $this->getReset($token)->with('email', $email)->with([
            'isLegacy' => $isLegacy,
            'legacyDestination' => $legacy_destination
        ]);

    }


    /**
     * Get current charges of user for current billing month
     *
     * @param $email
     * @return mixed
     */
    public function getCurrentCharges($email)
    {
        return $this->allocations->getCurrentCharges($email);
    }
}
