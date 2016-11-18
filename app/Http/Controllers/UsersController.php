<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Response;
use View;
use WA\DataStore\User\User;
use WA\Helpers\Traits\SetLimits;
use WA\Repositories\User\UserInterface;

/**
 * Users resource.
 *
 * @Resource("Users", uri="/users")
 */
class UsersController extends FilteredApiController
{
    use SetLimits;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * UsersController constructor.
     *
     * @param UserInterface $user
     * @param Request $request
     */
    public function __construct(
        UserInterface $user,
        Request $request
    ) {
        parent::__construct($user, $request);
        $this->user = $user;
    }


    public function numberUsers(Request $request)
    {
        $error['message'] = "Function not works yet";
        return response()->json($error);

        $conditions = $request->conditions;

        // Retrieve all the users that have the same companyId as the package.
        $users = User::where('companyId', $package->companyId);
        $usersAux = $users->get();

        $users->where(function ($query) use ($conditions, $usersAux) {
            foreach ($usersAux as $user) {
                $info = $this->retrieveInformationofUser($user);
                $ok = true;

                if ($conditions <> null) {
                    foreach ($conditions as $condition) {
                        foreach ($info as $i) {
                            if ($condition->name == $i['label'] && $ok) {
                                switch ($condition->condition) {
                                    case "like":
                                        $ok = $ok && strpos($i['value'], $condition->value) !== false;
                                        break;
                                    case "gt":
                                        $ok = $ok && ($i['value'] > $condition->value) ? true : false;
                                        break;
                                    case "lt":
                                        $ok = $ok && ($i['value'] < $condition->value) ? true : false;
                                        break;
                                    case "gte":
                                        $ok = $ok && ($i['value'] >= $condition->value) ? true : false;
                                        break;
                                    case "lte":
                                        $ok = $ok && ($i['value'] <= $condition->value) ? true : false;
                                        break;
                                    case "ne":
                                        $ok = $ok && ($i['value'] <> $condition->value) ? true : false;
                                        break;
                                    case "eq":
                                        $ok = $ok && ($i['value'] == $condition->value) ? true : false;
                                        break;
                                    default:
                                        $ok = $ok && true;
                                }
                            }
                        }
                    }
                }

                if ($ok) {
                    $query = $query->orWhere('id', $user->id);
                }
            }
        });

        if (!$this->includesAreCorrect($request, new PackageTransformer())) {
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        return $this->response()->withPaginator($packages->count(), new PackageTransformer(),
            ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
    }

    private function retrieveInformationofUser(User $user)
    {
        // Retrieve the package conditions.
        $udlValues = $user->UdlValues;

        // Retrieve the user information that will be compared.
        $info = array();
        $auxName = ["value" => $user->username, "name" => "name", "label" => "Name"];
        array_push($info, $auxName);
        $auxEmail = ["value" => $user->email, "name" => "email", "label" => "Email"];
        array_push($info, $auxEmail);
        $auxBudget = ["value" => "", "name" => "budget", "label" => "Budget"];
        array_push($info, $auxBudget);

        foreach ($udlValues as $uv) {
            $aux = ["value" => $uv->name, "name" => $uv->udl->name, "label" => $uv->udl->label];
            array_push($info, $aux);
        }

        $auxBudget = ["value" => "", "name" => "budget", "label" => "Budget"];
        array_push($info, $auxBudget);
        $auxCountry1 = ["value" => "", "name" => "country", "label" => "Country"];
        array_push($info, $auxCountry1);
        $auxCountry2 = ["value" => "", "name" => "country", "label" => "Country"];
        array_push($info, $auxCountry2);
        $auxCity = ["value" => "", "name" => "city", "label" => "City"];
        array_push($info, $auxCity);
        $auxAddress = ["value" => "", "name" => "address", "label" => "Address"];
        array_push($info, $auxAddress);

        return $info;
    }

    public function getLoggedInUser(Request $request)
    {
        $user = Auth::user();
    }

}
