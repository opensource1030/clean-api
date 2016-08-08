<?php

namespace WA\Services\Form\Dashboard;

use Session;
use Storage;
use App;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\JobStatusRepositoryInterface;
use WA\Services\Form\AbstractForm;
use WA\Repositories\DumpRepositoryInterface;


/**
 * Class DashboardForm
 * process all the dashboard form queries.
 */
class DashboardForm extends AbstractForm
{
    /**
     * @var \WA\Repositories\Company\CompanyInterface
     */
    protected $company;

    /**
     * The Company current in the session.
     *
     * @var \WA\Repositories\Company\CompanyInterface
     */
    protected $currentCompany = null;

    protected $notifyContainer = 'dashboard';

    /**
     * @var DumpRepositoryInterface
     */
    protected $dump;

    /**
     * @param CompanyInterface        $company
     * @param DashboardFormValidator  $validator
     * @param DumpRepositoryInterface $dump
     */
    public function __construct(
        CompanyInterface $company,
        DashboardFormValidator $validator,
        DumpRepositoryInterface $dump
    ) {
        $this->company = $company;
        $this->validator = $validator;
        $this->dump = $dump;
    }

    /**
     * Update the company's session.
     *
     * @param $input
     *
     * @return bool
     */
    public function updateCompanySession(array $input)
    {
        if (!$this->valid($input)) {
            $this->errors = $this->validator->errors();
            $this->notify('error', 'There was a problem with the selection, please try again');

            return false;
        }

        $currentCompany = $this->company->byId($input['companyId']);

        if (!$currentCompany) {
            $this->notify('error', 'We could not switch to that company, try again later');

            return false;
        }

        $this->notify('success', 'Company Updated to ' . $currentCompany->name);

        Session::set('clean.company', $currentCompany);
        
        $this->currentCompany = $currentCompany;

        return true;
    }


    /**
     * Update the CDI Mode
     *
     * @param int                          $mode | 1 => prospecting & 2 = normal
     * @param JobStatusRepositoryInterface $status | null
     *
     * @return bool
     */
    public function updateCDIMode($mode = 1, JobStatusRepositoryInterface $status = null)
    {
        $settings = config('settings');
        if ($settings['cdi']['mode'] === $mode) {
            return true;
        }

        //switch mode only if no data is being processed.
        $status = $status ?: App::make('WA\Repositories\JobStatusRepositoryInterface');
        $statusId = $status->getIdByName('Data Processing Complete');
        $dump = $this->dump->getSelectDump((int)$statusId);


        if (!empty($dump)) {
            $this->notify('error',
                'Cannot switch mode. Data is being actively processed for Data Dump #' . $dump->id . ', please try again later');

            return false;
        }


        $settings['cdi']['mode'] = $mode; //only call when it's prospecting
        $path = base_path() . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "settings.php";
        $values = var_export($settings, 1);

        if (!\File::put($path, "<?php return $values ;")) {
            return false;
        };

        return true;
    }

    /**
     * @return CompanyInterface
     */
    public function getCurrentCompany()
    {
        return $this->currentCompany;
    }
}
