<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Company\Company;
use WA\DataStore\Company\CompanyTransformer;
use WA\Repositories\Carrier\CarrierInterface;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Udl\UdlInterface;

/**
 * Class CompaniesController.
 */
class CompaniesController extends FilteredApiController
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
     * CompaniesController constructor.
     *
     * @param CompanyInterface $company
     * @param CarrierInterface $carrier
     * @param UdlInterface $udl
     * @param Request $request
     */
    public function __construct(
        CompanyInterface $company,
        CarrierInterface $carrier,
        UdlInterface $udl,
        Request $request
    ) {
        parent::__construct($company, $request);
        $this->company = $company;
        $this->carrier = $carrier;
        $this->udl = $udl;
    }


    /**
     * Create a new company.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $company = $this->company->create($data);
        if (!$company) {
            $error['errors']['post'] = 'Company could not be created. Please check your data';

            return response()->json($error)->setStatusCode(403);
        }

        return $this->response()->item($company, new CompanyTransformer(), ['key' => 'companies']);
    }

    /**
     * Update a company.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $company = Company::find($id);
        if (!isset($company)) {
            $error['errors']['put'] = Lang::get('messages.NotExistClass', ['class' => 'Company']);

            return response()->json($error)->setStatusCode(404);
        }
        $data = $request->all();
        $data['attributes']['id'] = $id;
        $company = $this->company->update($data['attributes']);
        if (!$company) {
            $error['errors']['put'] = 'Company could not be updated. Please check your data';

            return response()->json($error)->setStatusCode(403);
        }

        return $this->response()->item($company, new CompanyTransformer(), ['key' => 'companies']);
    }

    /**
     * Delete a company.
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCompany($id)
    {
        $company = Company::find($id);
        if (!isset($company)) {
            $error['errors']['delete'] = 'Company selected does not exist';
            return response()->json($error)->setStatusCode(404);
        }

        $this->company->delete($id);
        $company = Company::find($id);
        if (!$company) {
            return response()->json()->setStatusCode(204);
        } else {
            return response()->json()->setStatusCode(202);
        }
    }

}
