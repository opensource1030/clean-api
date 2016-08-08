<?php

namespace WA\Http\Controllers\Api;

use App;
use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Illuminate\Session\SessionManager as Session;
use Input;
use Response;
use View;
use WA\DataStore\Employee\EmployeeTransformer;
use WA\Helpers\Traits\SetLimits;
use WA\Repositories\Employee\EmployeeInterface;

/**
 * Employees resource.
 *
 * @Resource("Employees", uri="/employees")
 */
class EmployeesController extends ApiController
{
    use SetLimits;

    protected $employee;


    /**
     * @param EmployeeInterface $employee
     * @param Session           $session
     */
    public function __construct(
        EmployeeInterface $employee,
        Session $session = null
    ) {
        $this->employee = $employee;
        $this->session = $session ?: app()['session'];
    }

    /**
     * Show all employees
     *
     * Get a payload of all employees as reported by the companies imported census
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
        $employees = $this->employee->byPage();

        return $this->response()->withPaginator($employees, new EmployeeTransformer(), ['key' => 'employees']);
    }


    /**
     * Show a single employees
     *
     * Get a payload of a single employees as reported by the companies imported census
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $employee = $this->employee->byId($id);

        return $this->response()->item($employee, new EmployeeTransformer(), ['key' => 'employees']);

    }


    /**
     * Handles the datatables, this needs to be in a specific format to make it compatible
     * with the DataTale
     * ! overrides the default (dingo/api)
     * Returns employees based on company set.
     *
     * @return DataGrid
     */
    public function datatable()
    {
        $currentCompany = $this->session->get('clean.company');
        $currentCompanyId = $currentCompany->id;

        $this->setLimits();

        if (!empty($currentCompanyId)) {
            $employees = $this->employee->byCompanyId($currentCompanyId);
        } else {
            $employees = $this->employee->byPage(false);
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

        $response = DataGrid::make($employees, $columns, $options);

        return $response;
    }

}
