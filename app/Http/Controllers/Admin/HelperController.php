<?php

namespace WA\Http\Controllers\Admin;

use App;
use DB;
use Exception;
use Illuminate\Http\Request;
use Input;
use Redirect;
use View;
use WA\Helpers\DBSnapshot;
use WA\Helpers\Traits\SetLimits;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Repositories\FeatureRatePlanRepositoryInterface;

/**
 * Class HelperController.
 */
class HelperController extends AuthorizedController
{

    use SetLimits;


    public function index()
    {
        return View::make('admin.helpers.index');
    }

    /**
     * @return mixed
     */
    public function dbSnapshotIndex()
    {
        $path = base_path() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR .
            'seeds' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR;

        $db = \DB::connection()->getDatabaseName();
        $dumper = new \WA\Helpers\DBSnapshot();

        $tables = $dumper->getTables();

        return View::make('admin.helpers.db_snapshot')
            ->with('path', $path)
            ->with('db', $db)
            ->with('dumper', $dumper)
            ->with('tables', $tables);
    }
    public function dbSnapShot()
    {
        $dumper = new DBSnapshot();
        $query = \Input::get('query') ?: null;
        $tableName = \Input:: get('tableName');

        try {
            $result = $dumper->dumpTable($tableName);
            if ($result !== false) {
                \Alert::success('Snapshot of table ' . $tableName . ' successful.');
                \Alert::info('New filename: ' . $result);
            }
        } catch (Exception $e) {
            Alert::error('Something bad happened: ' . $e->getMessage());
        }

        return \Redirect::to('helpers/db-snapshot');
    }

    public function idGeneratorIndex()
    {
        $companies = \WA\DataStore\Company\Company::all();

        return View::make('admin.helpers.id_generator')
            ->with('companies', $companies);
    }

    public function showEasyVistaGeneration(Request $request)
    {
        if ($request->has('download')) {
            $file_path = $request->input('download');

            return response()->download($file_path);
        }

        $companies = app()->make('WA\Repositories\Company\CompanyInterface');


        return view('helpers.easyvista.index', ['type' => '', 'companies' => $companies->getActive()]);
    }

    public function processEasyVistaGeneration(Request $request)
    {
        $type = $request->input('type');

        switch ($type) {

            case 'bulk':
                return $this->processBulkEasyVistaGeneration($request);
                break;

            case 'single':
                return $this->processSingleEasyVistaGeneration($request);
                break;
        }
    }

    private function processBulkEasyVistaGeneration(Request $request)
    {
        $file = $request->file('easyvista-users');
        $file_name = date('YmdHs') . '_' . $file->getClientOriginalName();
        $file_name = str_replace(' ','_', $file_name);
        $file->move('/tmp/', $file_name);
        $file_path = '/tmp/' . $file_name;

        $columns = ['E_MAIL', 'CLEAN_ID', 'COMPANY_ID'];
        $table_name = "easyvista_bridge";

        DB::statement('DELETE FROM ' . $table_name);

        $this->importFile($file_path, $table_name, $columns);

        $employees = DB::table($table_name)
            ->where('CLEAN_ID', '')
            ->where('E_MAIL', '<>', '-')
            ->where('E_MAIL', '<>', '')
            ->groupBy('E_MAIL')
            ->get(['E_MAIL', 'COMPANY_ID']);

        $this->setLimits();
        foreach ($employees as $employee) {
            $clean_id = $this->generateIds($employee->COMPANY_ID);

            DB::table($table_name)
                ->where('E_MAIL', $employee->E_MAIL)
                ->update(['CLEAN_ID' => $clean_id]);
        }

        $export_file_name = 'export_' . $file_name;
        $path = '/tmp/' . $export_file_name;
        $this->exportFile($path, $table_name);

        $companies = app()->make('WA\Repositories\Company\CompanyInterface');

        $data = [
            'processed' => true,
            'filePath'  => $path,
            'companies' => $companies->getActive(),
            'type'      => 'bulk'
        ];

        return view('helpers.easyvista.index', $data);
    }

    private function processSingleEasyVistaGeneration(Request $request)
    {
        $companyId = $request->input('companyId');

        $id = $clean_id = $this->generateIds($companyId);

        $companies = app()->make('WA\Repositories\Company\CompanyInterface');


        $data = [
            'processed'         => true,
            'type'              => 'single',
            'id'                => $id,
            'companies'         => $companies->getActive(),
            'previousCompanyId' => (int)$companyId
        ];

        return view('helpers.easyvista.index', $data);
    }

    /**
     * Import the CSV file
     *
     * @param       $path
     * @param       $table
     * @param array $columns
     *
     * @return bool
     */
    protected function importFile($path, $table, array $columns)
    {
        $columns = '`' . implode("`,`", $columns) . '`';

        $db_name = getenv('DB_NAME');
        $pwd = getenv('DB_PASSWORD');
        $uname = getenv('DB_USERNAME');
        $db_host = getenv('DB_HOST');


        \DB::table($table)->truncate();

        $query = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE %s FIELDS TERMINATED BY ','
         OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (%s)", addslashes($path),
            $table, $columns);


        $pdoConn = new \PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $uname, $pwd, array(
            \PDO::MYSQL_ATTR_LOCAL_INFILE => true
        ));

        $smt = $pdoConn->prepare($query);

        if (!$smt->execute()) {

            return false;
        }

        return true;
    }

    /**
     * @param null $companyId
     *
     * @return mixed|null|string
     *
     * @throws Exception
     */
    public function generateIds($companyId = null)
    {
        $company_id = $companyId ?: Input::get('companyId');
        $row_count = (int)Input::get('rowCount') ?: 1;

        if (!is_numeric($row_count)) {
            throw new \Exception('You Must put in a number');
        }

        $ids = [];

        if (empty($company_id)) {
            return Redirect::back();
        }

        $companies = \WA\DataStore\Company\Company::all();
        $company = \WA\DataStore\Company\Company::whereId($company_id)->first();

        while ($row_count !== 0) {
            if (!empty($company)) {
                $ids[] = $this->randGenerator($company->shortName);
            }
            --$row_count;
        }

        if (!empty($companyId)) {
            $id = null;

            if (empty($company)) {
                $id = $this->randGenerator('NAN');
            } else {
                $id = $this->randGenerator($company->shortName);
            }

            $helpdesk = app()->make('WA\Repositories\HelpDesk\HelpDeskInterface');
            $dup_id = $helpdesk->employeeByIdentification($id);
            //check if there is an existing one in EV ::
            //@FIXME: this should only be checking our user DB when we go live
            if (count($dup_id)) {
                $this->generateIds($companyId);
            }

//
            return $id;
        }

        return View::make('admin.helpers.id_generator')
            ->with('companies', $companies)
            ->with('s_company', $company)
            ->with('ids', $ids);
    }

    /**
     * @param $salt
     *
     * @return mixed|string
     */
    public function randGenerator($salt, $length = 10, $seperator = '-')
    {

        $rand_id = crypt(uniqid(rand(), 100 ^ 2), $salt);
        $rand_id = strip_tags(stripslashes($rand_id));
        $rand_id = str_replace('.', '', $rand_id);
        $rand_id = strrev(str_replace('/', '', $rand_id));
        $rand_id = strtoupper(substr($rand_id, 0, $length));

        $rand_id = $salt . $seperator . $rand_id;

        return $rand_id;
    }

    /**
     * Quickly export CSV
     *
     * @param $path
     * @param $table
     *
     * @return bool
     */
    protected function exportFile($path, $table)
    {
        $db_name = getenv('DB_NAME');
        $pwd = getenv('DB_PASSWORD');
        $uname = getenv('DB_USERNAME');
        $db_host = getenv('DB_HOST');

        $query = "mysql -h$db_host  -u $uname  -p$pwd -e 'SELECT CONCAT_WS(\",\",`E_MAIL`,`CLEAN_ID`,`COMPANY_ID`) `E_MAIL,CLEAN_ID,COMPANY_ID ` FROM $table' $db_name > $path";

        // any other way, especially via PDO might fail silently.
        exec($query);

        return true;
    }

    public function exportAndGetUnsyncedEmployeesFile($companyId, $path = null)
    {
        $path = $path ?: storage_path('tmp/' . date('Ymdh') . '_' . $companyId . '_employees.csv');

        $db_name = getenv('DB_NAME');
        $pwd = getenv('DB_PASSWORD');
        $uname = getenv('DB_USERNAME');
        $db_host = getenv('DB_HOST');

        $query = "mysql -h$db_host  -u $uname  -p$pwd -e 'SELECT CONCAT_WS(\",\",`identification`,`email`,`firstName`,`lastName`) `Identification, Email, FirstName, LastName` FROM employees WHERE syncID is NOT NULL AND externalId IS NULL AND companyId = $companyId' $db_name > $path";

        // any other way, especially via PDO might fail silently.
        exec($query);

        return $path;
    }
    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        parent::__call($method, $parameters); // TODO: Change the autogenerated stub
    }

    /**
     *Generates a unique id for plans
     *
     * @param                                    $carrierId
     * @param FeatureRatePlanRepositoryInterface $featurePlan
     *
     * @return mixed|string
     */
    public function generatePlanId($carrierId, FeatureRatePlanRepositoryInterface $featurePlan = null)
    {
        $carrier = app()->make('WA\Repositories\Carrier\CarrierInterface');
        $carrier_name = $carrier->getShortNameById($carrierId);
        $plan_uid = $this->randGenerator($carrier_name, 7, '');

        // verify that the plan ID  doesn't exist
        $plan = $featurePlan ?: app()->make('WA\Repositories\FeatureRatePlanRepositoryInterface');
        $existing_id = $plan->byPlanId($plan_uid);

        if (!is_null($existing_id)) {
            $this->generatePlanId($carrierId);
        }

        return $plan_uid;
    }
}
