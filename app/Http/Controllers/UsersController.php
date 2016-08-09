<?php

namespace WA\Http\Controllers\Api;

use App;
use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Illuminate\Session\SessionManager as Session;
use Input;
use Response;
use View;
use WA\DataStore\User\UserTransformer;
use WA\Helpers\Traits\SetLimits;
use WA\Repositories\User\UserInterface;

/**
 * Users resource.
 *
 * @Resource("Users", uri="/users")
 */
class UsersController extends ApiController
{
    use SetLimits;

    protected $user;


    /**
     * @param UserInterface $user
     * @param Session           $session
     */
    public function __construct(
        UserInterface $user,
        Session $session = null
    ) {
        $this->employee = $user;
        $this->session = $session ?: app()['session'];
    }

    /**
     * Show all users
     *
     * Get a payload of all users as reported by the companies imported census
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
        $users = $this->employee->byPage();

        return $this->response()->withPaginator($users, new UserTransformer(), ['key' => 'users']);
    }


    /**
     * Show a single users
     *
     * Get a payload of a single users as reported by the companies imported census
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $user = $this->employee->byId($id);

        return $this->response()->item($user, new UserTransformer(), ['key' => 'users']);

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
            $users = $this->employee->byCompanyId($currentCompanyId);
        } else {
            $users = $this->employee->byPage(false);
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
