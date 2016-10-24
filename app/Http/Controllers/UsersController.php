<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Response;
use View;
use WA\DataStore\User\User;
use WA\DataStore\User\UserTransformer;
use WA\Helpers\Traits\SetLimits;
use WA\Repositories\User\UserInterface;

use WA\DataStore\User\User;

use Illuminate\Support\Facades\Lang;

/**
 * Users resource.
 *
 * @Resource("Users", uri="/users")
 */
class UsersController extends ApiController
{
    use SetLimits;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(
        UserInterface $user
    ) {
        $this->user = $user;
    }

    /**
     * Show all users.
     *
     * @Get("/")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10),
     *       @Parameter("access_token", required=true, description="Access token for authentication")
     * })
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->user->setCriteria($criteria);

        $users = $this->user->byPage();

        $response = $this->response()->withPaginator($users, new UserTransformer(), ['key' => 'users']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single users.
     *
     * Get a payload of a single users
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->user->setCriteria($criteria);

        $user = $this->user->byId($id);

        return $this->response()->item($user, new UserTransformer($criteria), ['key' => 'users']);
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

        return $this->response()->withPaginator($packages->count(), new PackageTransformer(), ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
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
        var_dump($user);
    }

    /**
     * Handles the datatables, this needs to be in a specific format to make it compatible
     * with the DataTale
     * ! overrides the default (dingo/api)
     * Returns users based on company set.
     *
     * @return DataGrid
     */
    public function datatable()
    {
        $currentCompany = $this->session->get('clean.company');
        $currentCompanyId = $currentCompany->id;

        $this->setLimits();

        if (!empty($currentCompanyId)) {
            $users = $this->user->byCompanyId($currentCompanyId);
        } else {
            $users = $this->user->byPage(false);
        }

        $columns = [
            'id',
            'identification',
            'firstName',
            'lastName',
            'email',
            'supervisorEmail',
            'companyName',
        ];

        $options = [
            'throttle' => $this->defaultQueryParams['_perPage'],
            'method' => $this->defaultQueryParams['_method'],
        ];

        $this->setLimits();

        $response = DataGrid::make($users, $columns, $options);

        return $response;
    }
}
