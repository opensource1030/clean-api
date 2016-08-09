<?php

namespace WA\Http\Controllers\Auth;

use LucaDegasperi\OAuth2Server\Authorizer;
use Session;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector as Redirect;
use WA\Http\Controllers\BaseController;
use WA\Services\Form\Dashboard\DashboardForm;
use WA\Services\Form\Login\LoginForm;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Log;

/**
 * Class AuthController.
 */
class AuthController extends BaseController
{
    /**
     * @var \WA\Services\Form\Login\LoginForm
     */
    protected $loginForm;

    protected $redirectPath = '/dashboard';

    protected $loginPath = '/login';

    protected $redirectTo = '/';

    use ResetsPasswords;
    use ValidatesRequests;

    /**
     * @param LoginForm $loginForm
     */
    public function __construct(LoginForm $loginForm)
    {
        $this->loginForm = $loginForm;
    }

    public function index()
    {
        return view('auth.login');
    }

    /**
     * @param Redirect $redirect
     * @param Request  $request
     *
     * @return \Illuminate\View\View
     */
    public function login(Redirect $redirect, Request $request)
    {
        $email = $this->getEmail();
        $formOptions = $this->hideOrNot();

        $isLegacy = (bool)$request->input('legacy');
        $legacy_destination = $request->input('legacy_dest');

        if (!$this->loginForm->loggedIn()) {
            return view('auth.login')
                ->with([
                    'isLegacy' => $isLegacy,
                    'legacyDestination' => $legacy_destination,
                    'formOptions' => $formOptions,
                    'email' => $email
                ]);
        }

        return $redirect->to('dashboard');
    }

    /**
     * Attempt a login
     *
     * @param Request       $request
     * @param Redirect      $redirect
     * @param Auth          $auth
     * @param DashboardForm $dashboard
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doLogin(Request $request, Redirect $redirect, Auth $auth, DashboardForm $dashboard = null)
    {
        $credentials = $request->only('email', 'password');
//
        if (!$this->loginForm->login($credentials, $request->has('remember'))) {
            Session::set('employee_exists', 'yes');
            return $redirect->to($this->loginPath())
                ->withInput($request->except('password'))
                ->with('error', $this->loginForm->errors());
        }

        $user = $auth->user();

        if (empty($user)) {
            return false;
        }

        $defaultLang = $auth->user()->defaultLang ?: 'en';
        $defaultCurrency = $auth->user()->location->currencyIso;

        $api_token = md5(uniqid(rand(), true));
        $user->apiToken = $api_token;
        $user->save();

        if (!empty($company = $user->company)) {

            $dashboard = app()->make('WA\Services\Form\Dashboard\DashboardForm');
           // $dashboard->updateCompanySession(['companyId' => $company->id]);
        }

        Session::set('defaultLang', $defaultLang);
        Session::set('nativeCurrency', $defaultCurrency);
        //Set users gravatar image as profile pic, if else generate random one.
        $email = $user->email;
        $hash = md5(strtolower(trim($email)));
        $profileImg = "http://www.gravatar.com/avatar/" . $hash;
        Session::set('profileImg', $profileImg);


        if ($request->ajax()) {

            return Response::json(['token' => $user->apiToken, 'employee' => $user->toArrry()]);
        }


        return $redirect->route('dashboard.index');
    }

    /**
     * Attempt to confirm account with code.
     *
     * @param string $code
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm($code)
    {
        //@TODO: implement per new Illuminate/Auth
        return true;
//        if (Confide::confirm($code)) {
//            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
//            return Redirect::action('UsersController@login')
//                           ->with('notice', $notice_msg);
//        } else {
//            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
//            return Redirect::action('UsersController@login')
//                           ->with('error', $error_msg);
//        }
    }

    /**
     * Displays the forgot password form.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $isLegacy = (bool)$request->input('legacy');
        $legacy_destination = $request->input('legacy_dest');

        return view('auth.password')
            ->with([
                'isLegacy' => $isLegacy,
                'legacyDestination' => $legacy_destination
            ]);

    }

    /**
     * Attempt to send change password link to the given email.
     *
     * @return \Illuminate\Http\Response
     */
    public function doForgotPassword(Request $request)
    {
        $a = [];
        return $this->loginForm->sendPasswordResetLink($request);
    }

    /**
     * Shows the change password form with the given token.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword($token, Request $request)
    {
        return $this->loginForm->resetRequest($token, $request);
    }

    /**
     * Attempt change password of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function doResetPassword(Request $request, Redirect $redirect)
    {
        $isLegacy = (bool)$request->input('legacy');
        $redirect_base = route('login');
        $redirect_path = (bool)$request->input('legacyDestination') ? $request->input('legacyDestination') : $redirect_base;

        if ($this->loginForm->resetUserPassword($request)) {

            return view('auth.redirect_legacy')->with('loginUrl', $redirect_path);

        } else {
            $error_msg = trans('users.alerts.wrong_password_reset');

            return $redirect->to($this->resetPassword(), array('token' => $request->input('token')))
                ->withInput()
                ->with('error', $error_msg);

        }
    }

    /**
     * @param Redirect $redirect
     *
     * @return mixed
     */
    public function logout(Redirect $redirect)
    {
        $this->loginForm->logout();

        return $redirect->route('login');
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    public function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
    }

    /**
     * Choose the part of the form showed to the user.
     */
    private function hideOrNot(){
        switch (Session::pull('employee_exists')) {
         
            case 'yes':
                //Log::info("YES");
                return $this->loginUserHidden();
                break;
         
            case 'no':
                //Log::info("NO");
                return $this->registerUserHidden();
                break;
         
            default:
                //Log::info("DEFAULT");
                return $this->initialLoginPage();
                break;
        }
    }

    /**
     * Returns the properties of each case.
     *
     * @return: Array.
     */
    private function loginUserHidden(){
        return array(
            'loginForm' => 'enabled',
            'loginEmail' => 'disabled',
            'loginPassword' => 'enabled',
            'loginRemember' => 'enabled',
            'loginForgot' => 'enabled',
            'loginLoginButton' => 'enabled',
            'loginContinueButton' => '',
            'registerForm' => '',
            'backButton' => 'enabled'
        );
    }

    private function registerUserHidden(){
        return array(
            'loginForm' => '',
            'loginEmail' => '',
            'loginPassword' => '',
            'loginRemember' => '',
            'loginForgot' => '',
            'loginLoginButton' => '',
            'loginContinueButton' => '',
            'registerForm' => 'enabled',
            'backButton' => 'enabled'
        );
    }

    private function initialLoginPage(){
        return array(
            'loginForm' => 'enabled',
            'loginEmail' => '',
            'loginPassword' => '',
            'loginRemember' => 'enabled',
            'loginForgot' => '',
            'loginLoginButton' => '',
            'loginContinueButton' => 'enabled',
            'registerForm' => '',
            'backButton' => ''
        );
    }

    /**
     * Get the Email from Session.
     *
     * @return: Email.
     */
    private function getEmail(){
        return Session::pull('email');
    }
}