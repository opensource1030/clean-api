<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Company\Company;
use WA\DataStore\Company\CompanyTransformer;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Udl\UdlInterface;
use DB;
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
        UdlInterface $udl,
        Request $request
    ) {
        parent::__construct($company, $request);
        $this->company = $company;
        $this->udl = $udl;
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
        $success = true;

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'companies')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data'];
            $data['attributes']['id'] = $id;
            $company = $this->company->update($data['attributes']);

            if ($company == 'notExist') {
                DB::rollBack();
                $error['errors']['company'] = Lang::get('messages.NotExistClass', ['class' => 'Company']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if ($company == 'notSaved') {
                DB::rollBack();
                $error['errors']['company'] = Lang::get('messages.NotSavedClass', ['class' => 'Company']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['company'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Company', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships']) && $success) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['address']) && $success) {
                if (isset($dataRelationships['address']['data'])) {
                    $dataAddress = $this->parseJsonToArray($dataRelationships['address']['data'], 'address');
                    try {
                        $company->address()->sync($dataAddress);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['address'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Company', 'option' => 'created', 'include' => 'Address']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();

            return $this->response()->item($company, new CompanyTransformer(),
                ['key' => 'companies'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
    /**
     * Create a new company.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {   
        $success = true;
       
        if (!$this->isJsonCorrect($request, 'companies')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
        else{
            $data = $request->all()['data'];
        }

        DB::beginTransaction();

        try {
            $company = $this->company->create($data['attributes']);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Company', 'option' => 'created', 'include' => '']);
            $error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships']) && $success) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['address']) && $success) {
                if (isset($dataRelationships['address']['data'])) {
                    $dataAddress = $this->parseJsonToArray($dataRelationships['address']['data'], 'address');
                    try {
                        $company->address()->sync($dataAddress);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['address'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Company', 'option' => 'created', 'include' => 'Address']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if($success){
            DB::commit();
            return $this->response()->item($company, new CompanyTransformer(),
                ['key' => 'companies'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
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
        if ($company != null) {
            $this->company->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Company']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $company = Company::find($id);
        if ($company == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Company']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

}
