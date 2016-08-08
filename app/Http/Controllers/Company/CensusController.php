<?php

namespace WA\Http\Controllers\Company;

use Illuminate\Contracts\Routing\ResponseFactory as Response;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Jobs\CompanyCensus;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Employee\EmployeeInterface;
use WA\Services\Form\Company\CensusForm;

/**
 * Class CensusController.
 *
 * Takes care of the updating and management of companies census
 */
class CensusController extends AuthorizedController
{
    use DispatchesJobs;

    /**
     * @var \WA\Services\Form\Company\CensusForm
     */
    protected $censusForm;

    /**
     * @var \WA\Repositories\Census\CensusInterface
     */
    protected $census;

    /**
     * @var \WA\Repositories\Company\CompanyInterface
     */
    protected $company;

    /**
     * @var \WA\Repositories\Employee\EmployeeInterface
     */
    protected $employee;

    /**
     * notification bucket.
     *
     * @var
     */
    protected $notifyContainer = 'census';

    /**
     * @param CensusForm        $censusForm
     * @param CompanyInterface  $company
     * @param EmployeeInterface $employee
     */
    public function __construct(
        CensusForm $censusForm,
        CompanyInterface $company,
        EmployeeInterface $employee
    ) {
        parent::__construct();
        $this->censusForm = $censusForm;
        $this->company = $company;
        $this->employee = $employee;

        $this->data['notifyContainer'] = $this->notifyContainer;
    }

    public function validateCensus(Request $request)
    {
        if (is_null($request->input('companyId'))) {
            $this->censusForm->notify('error', 'CSV file corrupted, please reload');

            return redirect()->back();
        }

        $companyName = studly_case($this->company->byId($request->input('companyId'))['name']);
        $storage_path = 'clients' . DIRECTORY_SEPARATOR . $companyName . DIRECTORY_SEPARATOR . 'census';

        $file = $request->file('census');

        $file_name = null;

        if (!is_null($file)) {
            $file_name = $file->getClientOriginalName();
            $file = $file->move(storage_path($storage_path), $file_name);
            $filePath = $file->getRealPath();
        } else {
            $filePath = $request->session()->get('census_file');
        }

        if (empty($file) && empty($filePath)) {
            $this->censusForm->notify('error', 'Please upload a file');

            return redirect()->back();
        }


        $file_extension = explode('.', $filePath)[1];

        if (empty($file_name)) {
            $file_name = explode(DIRECTORY_SEPARATOR, $filePath);
            $file_name = explode('.', $file_name[count($file_name) - 1])[0];
        }

        if ($file_extension !== 'csv') {
            $this->censusForm->notify('error', 'You need to load a CSV file, please try again');

            return redirect()->back();
        }

        $companyId = $request->input('companyId');

        $request->session()->set('validating_census', 1);
        $request->session()->set('census_file', $filePath);

        if (!empty($file_name)) {
            $request->session()->set('file_name', $file_name);
        }

        if (!$this->censusForm->hasMapping($companyId)) {
            return redirect()->route(
                'mappers.census.create',
                [
                    'file' => $filePath,
                    'companyId' => $request->input('companyId')
                ]
            );
        }

        // Send to confirm map
        return redirect()->route(
            'mappers.census.edit',
            [
                'companyId' => $companyId,
                'file' => $filePath,
                'status' => 'validating'
            ]
        );
    }

    public function create(Request $request)
    {
        $input = $request->all();

        $input['mapping'] = json_decode($this->censusForm->getMapping($input['companyId'])['headers'])->mapping;
        $input['companyName'] = $this->censusForm->getCompanyName($input['companyId']);

        if (!$this->censusForm->save($input)) {
            return $this->index();
        };

        $input['censusId'] = $this->censusForm->createCensus([
            'companyId' => $input['companyId'],
            'file' => $request->session()->get('file_name')
        ]);

        $input['rules'] = $this->censusForm->getRules($input['companyId']);
        $input['summaries'] = $this->censusForm->getPreProcessSummaries($input['companyId'], $input['censusId']);

        return $this->showRules($input);

    }

    public function index()
    {
        if (!isset($this->currentCompany)) {
            throw new \InvalidArgumentException("No Company Selected. Please select a company and try again");
        }

        $unsynced = $this->company->getEmployeesCount($this->currentCompany->id, false);

        $data = array_merge($this->data, [
            'companies' => $this->company->byPage(false)->toArray(),
            'currentCompany' => $this->currentCompany,
            'censuses' => $this->company->getCensuses($this->currentCompany->id),
            'issues' => $unsynced,
            'externalSystem' => 'Easyvista'
        ]);

        return view('companies.census.index', $data);
    }

    /**
     * Show rules that the census will be processed on and also the summary of the previous
     *
     * @param array $input
     *
     * @return \Illuminate\View\View
     */
    public function showRules(array $input)
    {
        return view('companies.census.rules', $input);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function process(Request $request)
    {
        $companyId = $request->input('companyId');
        $censusId = $request->input('censusId');

        $this->dispatch(new CompanyCensus($companyId, $censusId));

        return redirect()->route('census.index');
    }

    public function getTemplate(Response $response)
    {
        $file = storage_path('clients/company_census_template.xlsx');

        return $response->download($file);
    }

    public  function getUnSyncedEmployees($companyId, Response $response)
    {
        $helper = app()->make('WA\Http\Controllers\Admin\HelperController');
        $file = $helper->exportAndGetUnsyncedEmployeesFile($companyId);

        return $response->download($file);
    }

}
