<?php

namespace WA\Http\Controllers\Api;

use Dingo\Api\Http\Response;
use WA\DataStore\Company\CompanyTransformer;
use WA\Repositories\Carrier\CarrierInterface;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Udl\UdlInterface;
use Cartalyst\DataGrid\Laravel\Facades\DataGrid;

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
     * @param UdlInterface     $udl
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
        $companies  = $this->company->byPage();

        return $this->response()->paginator($companies, new CompanyTransformer(),['key' => 'companies']);

    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function show($id)
    {
        $company = $this->company->byId($id);


        return $this->response()->item($company, new CompanyTransformer(),['key' => 'companies']);

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