<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Dingo\Api\Http\Response;
use WA\DataStore\Company\Company;
use Faker\Provider\hr_HR\Company;
use WA\DataStore\Company\CompanyTransformer;
use WA\Repositories\Carrier\CarrierInterface;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Udl\UdlInterface;
use Illuminate\Http\Request;

/**
 * Class CompaniesController.
 */
class CompaniesController extends ApiController
{
    /**
     * @var CompanyInterface
     */
    protected $company;

    /**
     * @var CarrierInterface
     */
    protected $carrier;

    /**
     * @var UdlInterface
     */
    protected $udl;


    /**
     * @param CompanyInterface $company
     * @param CarrierInterface $carrier
     * @param UdlInterface $udl
     */
    public function __construct(CompanyInterface $company, CarrierInterface $carrier, UdlInterface $udl)
    {
        $this->company = $company;
        $this->carrier = $carrier;
        $this->udl = $udl;
    }

    /**
     * @return Response
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->company->setCriteria($criteria);
        $company = $this->company->byPage();
      
        $response = $this->response()->withPaginator($company, new CompanyTransformer(),['key' => 'companies']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function show($id)
    {
        $company = Company::find($id);
        if($company == null){
            $error['errors']['get'] = 'the Company selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

        return $this->response()->item($company, new CompanyTransformer(), ['key' => 'companies']);
    }

    /**
     * Create a new company
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $company = $this->company->create($data);
        return $this->response()->item($company, new CompanyTransformer(), ['key' => 'companies']);
    }

    /**
     * Update a company
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $data = $request->all();
        $data['id'] = $id;
        $company = $this->company->update($data);
        return $this->response()->item($company, new CompanyTransformer(), ['key' => 'companies']);
    }

    /**
     * Delete a company
     *
     * @param $id
     */
    public function deleteCompany($id)
    {
        $this->company->delete($id);
        $this->index();
    }


    /**
     * Handles the datatables, this needs to be in a specific format to make it compatible
     * with the DataTale
     * ! overrides the default (dingo/api)
     * Returns all companies
     *
     * @return DataGrid
     */
    public function datatable()
    {
        $companies = $this->company->getAll(false);

        $columns = [
            'id',
            'name',
            'label',
            'shortName',
            'active',
        ];

        $options = [
            'throttle' => $this->defaultQueryParams['_perPage'],
        ];

        $response = DataGrid::make($companies, $columns, $options);
        return $response;
    }
}
