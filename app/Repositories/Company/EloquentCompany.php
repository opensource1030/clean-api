<?php

namespace WA\Repositories\Company;

use Illuminate\Database\Eloquent\Model;
use Log;
use WA\Repositories\AbstractRepository;
use WA\Repositories\Carrier\CarrierDetailInterface;
use WA\Repositories\Carrier\CarrierInterface;
use WA\Repositories\Census\CensusInterface;
use WA\Repositories\Device\DeviceInterface;
use WA\Repositories\User\UserInterface;
use WA\Repositories\Udl\UdlInterface;
use WA\Services\Form\Company\CompanyForm;


class EloquentCompany extends AbstractRepository implements CompanyInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \WA\Repositories\User\UserInterface
     */
    protected $user;


    /**
     * @var \WA\Repositories\Census\CensusInterface
     */
    protected $census;

    /**
     * @var \WA\Repositories\Udl\UdlInterface
     */
    protected $udl;

    /**
     * @var DeviceInterface
     */
    protected $device;

    /**
     * @var CarrierInterface
     */
    protected $carrier;

    /**
     * @var CarrierDetailInterface
     */
    protected $carrierDetail;

    protected $account;

    protected $domainsTable = 'company_domains';

    /**
     * @param Model                  $model
     * @param UserInterface      $user
     * @param CensusInterface        $census
     * @param UdlInterface           $udl
     * @param CarrierInterface       $carrier
     * @param DeviceInterface        $device
     * @param CarrierDetailInterface $carrierDetail
     */
    public function __construct(
        Model $model,
        UserInterface $user,
        CensusInterface $census,
        UdlInterface $udl,
        CarrierInterface $carrier,
        DeviceInterface $device,
        CarrierDetailInterface $carrierDetail
    ) {
        $this->model = $model;
        $this->user = $user;
        $this->census = $census;
        $this->udl = $udl;
        $this->device = $device;
        $this->carrier = $carrier;
        $this->carrierDetail = $carrierDetail;
    }


    /**
     * Get All Companies.
     *
     * @param bool $paginate
     * @param int  $perPage
     *
     *
     * @return Object as Collection of object information, will return paginated if pagination is true
     */
    public function getAll($paginate = true, $perPage = 25)
    {
        $model = $this->model; //Get All Companies including prospects
        $model = $model->orderBy('name', 'ASC');

        if (!$paginate) {
            //
            return $model->get();
        }

        return $model->paginate($perPage);
    }

    /**
     * Wrapper function.
     *
     * @param $name
     *
     * @return Object
     */
    public function getByName($name)
    {
        return $this->byName($name);
    }

    /**
     * Get the company information by its name.
     *
     * @param $name
     *
     * @return Object of the company information
     */
    public function byName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Creates new employee for a company.
     *
     * @param int   $id of the company
     * @param array $user
     *
     * @return bool true successful | false
     */
    public function addUser($id, array $user)
    {
        return $this->user->create($user, $user['udlValues']);
    }

    /**
     * Update an employee for a company.
     *
     * @param array $user
     *
     * @return bool true successful | false
     */
    public function updateUser(array $user)
    {
        return $this->user->update($user);
    }



    /**
     *  Update a census record for a company.
     *
     * @param int    $id      of company
     * @param int    $censusId
     * @param string $status
     * @param array  $options to update census with
     *
     * @return bool
     */
    public function updateCensus($id, $censusId, $status, $options = [])
    {
        return $this->census->update($censusId, $id, $status, $options);
    }

    /**
     * Syncs up employee on every census load for a company.
     *
     * @param $censusId
     * @param $companyId
     *
     * @return bool
     */
    public function syncUserSupervisor($censusId, $companyId)
    {
        $updatedUsers = $this->user->byCensus($censusId, $companyId);

        foreach ($updatedUsers as $user) {

            $synced = $this->user->syncSupervisor($user->email, $user->supervisorEmail);

            if (!$synced) {
                continue;
            }

        }

        return true;
    }

    /**
     * Creates UDLs for a company.
     *
     * @param int   $id of company
     * @param array $udls
     *
     * @return bool
     */
    public function createUDLs(
        $id,
        array $udls
    ) {
        foreach ($udls as $udl) {
            if (!$this->udl->create($udl, $id, $udl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the Most Recent census for a company.
     *
     * @param int $companyId
     *
     * @return Object of the census information
     */
    public function getRecentCensus(
        $companyId
    ) {
        return $this->census->byCompany($companyId, true);
    }

    /**
     * Get a company's account summary information.
     *
     * @param $id
     *
     * @return Object object of account information
     */
    public function getAccountSummariesById($id)
    {
        return $company = $this->account->byCompanyAll($id);
    }

    /**
     * Get the carriers for this company.
     *
     * @param $id
     *
     * @return Object object of company
     */
    public function getCarriers($id)
    {
        return $this->carrier->byCompany($id);
    }

    public function getDevices(
        $id
    ) {
        return $this->device->byCompany($id);
    }

    /**
     * Get a company's carrier details by month.
     *
     * @param $id
     * @param $billMonth
     * @param $carrierId
     *
     * @return Object object of company carrier details
     */
    public function getCarrierDetails(
        $id,
        $carrierId,
        $billMonth
    ) {
        return $this->carrierDetail->byCompany($id, $carrierId, $billMonth);
    }

    /**
     * Get all active companies.
     *
     * @param int|true $isActive
     *
     * @return mixed
     */
    public function getActive($isActive = 1)
    {
        return $this->model->whereActive($isActive)->orderBy('name', 'asc')->get();
    }

    /**
     * Get a company's account details.
     *
     * @param $id
     * @param $billingAccountNumber
     * @param $billMonth
     */
    public function getAccountDetails($id, $billingAccountNumber, $billMonth)
    {
        return $this->account->byCompany($id, $billingAccountNumber, $billMonth);
    }

    /**
     * Get a company's UDLs.
     *
     * @return array of UDL and Values
     */
    public function getUDLs($id, $api = false)
    {
        if ($api) {
            return $this->udl->byCompanyId($id, false, $api);
        }

        $udls = $this->udl->byCompanyId($id);

        return $udls;
    }

    /**
     * Get the class transformer.
     */
    public function getTransformer()
    {
        return $this->model->getTransformer();
    }

    /**
     * Given some UDL, it gets the matching path ID.
     *
     * @param int   $id         company id
     * @param array $udls       the values to match
     * @param bool  $externalId should return the externalId instead | false
     * @param int   $creatorId  current user id
     * @param array $userInfo   Info of user being created/edited
     *
     * @return int ID value if there is a match | null id there is no match found
     */
    public function getUdlValuePathId($id, array $udls, $externalId = false, $creatorId, array $userInfo)
    {
        $udlValuePath = app()->make('WA\Repositories\UdlValuePath\UdlValuePathInterface');
        $udlValuePathUsers = app()->make('WA\Repositories\UdlValuePathUsers\UdlValuePathUsersInterface');
        $udl_path_rule = $this->model->where('id', $id)->pluck('udlPathRule');
        $udl_path_stack = $this->splitUdlPathRule($udl_path_rule);
        $company_name = $udl_path_stack[0]; // first in the stack is always the company
        $lookup_string = $this->getLookupString($udls, $udl_path_rule, $id);
        $creatorId = (!empty($creatorId)) ? $creatorId : null;
        $userLastName = (!empty($userInfo['lastName'])) ? $userInfo['lastName'] : null;
        $userFirstName = (!empty($userInfo['firstName'])) ? $userInfo['firstName'] : null;
        $userEmail = (!empty($userInfo['email'])) ? $userInfo['email'] :
            strtolower($userFirstName) . "." . strtolower($userLastName) . '@' . strtolower($company_name) . '.com';
        $companyUserId = (!empty($userInfo['companyUserIdentifier'])) ? $userInfo['companyUserIdentifier'] : null;


        //Default to company name if lookup string returns empty
        if (empty($lookup_string)) {
            $lookup_string = $this->model->where('id', $id)->pluck('name');
        }

        try {
            $udl_path = $udlValuePath->byUdlPath($lookup_string);

            if (empty($udl_path['id'])) {
                //create the entry
                $udl_path = $udlValuePath->create([
                    'udlPath' => $lookup_string,
                    'udlId' => count($udl_path_stack) - 1, // use the last udlId in the stack
                ]);
            }


            if ($externalId) {
                $id = $udl_path['externalId'];
                // if we didn't get anything, revert to the default level, company
                if (empty($id)) {
                    $id = $udlValuePath->byUdlPath($company_name)['externalId'];
                }
            } else {
                $id = $udl_path['id'];

            }

            if (empty($udl_path['id']) || (!$externalId && empty($udl_path['externalId']))) {
                //Store creator and user info for udl path
                $udlValuePathUsers->create([
                    'udlValuePathId' => $udl_path['id'],
                    'creatorId' => $creatorId,
                    'userEmail' => $userEmail,
                    'userFirstName' => $userFirstName,
                    'userLastName' => $userLastName,
                    'userUserId' => $companyUserId,
                ]);
            }


            return $id;
        } catch (\Exception $e) {
            \Log::error('Getting the UDL Value Path Failed: ' . $e->getMessage());
        }
    }

    /**
     * Splits a UDL path, niftly.
     *
     * @param string $path
     * @param array  $delimiters
     *
     * @return array
     */
    private function splitUdlPathRule($path, array $delimiters = ["\/", '-'])
    {
        $delimit_by = implode('|', $delimiters);

        return preg_split("/($delimit_by)/", $path);
    }

    /**
     * Given a UDL string, it parses the the string based on the company.
     *
     * @param array  $udls to construct the values from
     * @param string $udlPathRule
     *
     * @return string of the UDL oto search for
     */
    private function getLookupString(array $udls, $udlPathRule, $companyId = null)
    {
        $udl_path_stack = $this->splitUdlPathRule($udlPathRule);
        array_shift($udl_path_stack);

        $udlValue = app()->make('WA\Repositories\UdlValue\UdlValueInterface');
        $replaceablePlaceHolders = [];

        foreach ($udl_path_stack as $stack) {
            foreach ($udls as $udl) {
                $val = $this->trimPlaceHolder($stack);

                if (empty($udl[$val]['value'])) {
                    continue;
                }

                $new_udl_value = $udlValue->byId($udl[$val]['value']) ?: $udlValue->byNameOrCreate($udl[$val]['value'],
                    $val, $companyId);
                $new_udl_value_name = $this->trimUdlName($new_udl_value['name']);
                $replaceablePlaceHolders[$stack] = isset($new_udl_value_name) ? str_replace('/', '\\',
                    $new_udl_value_name) : ''; // we currently delimit the look-ups by "/"
            }
        }

        $lookup_string = strtr($udlPathRule, $replaceablePlaceHolders);

        // we don't want to return the rule..., so just return the company
        if ($lookup_string === $udlPathRule) {
            return explode("/", $lookup_string)[0];
        }

        return $lookup_string;
    }

    /**
     * removes the << >>placeholders from the value.
     *
     * @param $val
     *
     * @return string of trimmed values
     */
    private function trimPlaceHolder($val)
    {
        return trim(str_replace('>>', '', str_replace('<<', '', $val)));
    }

    /**
     * removes the \\ from the udl name.
     *
     * @param $name
     *
     * @return string of trimmed values
     */
    private function trimUdlName($name)
    {
        return trim(str_replace('\\', ' ', $name));
    }

    /**
     * Get the list of internal UDL tables that an external system can map to by companyId.
     *
     * @param int $companyId
     *
     * @return array
     */
    public function getMappableUdlFields($companyId)
    {
        $company = $this->model->where('id', $companyId)->first();

        $udls = $company->udls->toArray();
        $mappableFields = [];

        foreach ($udls as $udl) {
            $mappableFields[$udl['name']] = $udl['name'];
        }

        return $mappableFields;
    }

    /**
     * Get all census for a company
     *
     * @param int $companyId
     *
     * @return Object collection of all census information
     */
    public function getCensuses($companyId)
    {
        return $this->census->byCompany($companyId, false, 5, false);
    }

    protected function syncUsers(Model $company, array $users)
    {
        try {
            foreach ($users as $user) {
                $company->save($user);
            }
        } catch (\Exception $e) {
            Log::error('Error attach employee to company: ' . $e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Get the total amount of employee
     *
     * @param int  $id   of the company
     * @param bool $sync with external system (EasyVista in our case)
     *
     * @return int count of employee
     */
    public function getUsersCount($id, $sync = true)
    {
        $company = $this->byId($id);

        if (!$sync) {
            $c = $company->users()
                ->whereNotNull('syncId')
                ->where('externalId', null)->count();

            return $c;
        }

        $count = $company->employeesCount;

        return $count;
    }


    /**
     *  Get the active raw data version for a company.
     *
     * @param int    $id          of the company
     * @param string $dataMapType {ivd | cdr | wls | als | inv | census}
     * @param bool   $active      | true
     *
     * @return string of the version (defaults to active)
     */
    public function getMapVersion($id, $dataMapType, $active = true)
    {
        $dataMap = $this->dataMap->byCompany($id, $dataMapType, $active);

        if (!$dataMap) {
            return false;
        }


        return $dataMap->versionId;
    }

    /**
     * Get respective rule
     *
     * @param int    $companyId
     * @param string $type
     *
     * @return array of rules
     */
    public function getCensusRules($companyId, $type)
    {
        $pivot_name = 'company_rules.priority';

        $rule = $this->model->where('id', $companyId)
            ->first()
            ->rules()->where('type', $type)
            ->orderBy($pivot_name, 'asc')
            ->get()
            ->toArray();

        return $rule;
    }

    /**
     * Get live/demo status of a company
     *
     * @param $id
     */
    public function getLiveStatus($id)
    {
        return $this->model->where('id', $id)->pluck('active');
    }

    /*
     * Create a New Company
     * @param array     $data
     * @param CompanyForm $companyForm
     *
     * @return Object of the company | false
     */
    public function create(
        array $data,
        CompanyForm $companyForm = null
    ) {

        $companyData = [
            'name' => isset($data['name']) ? $data['name'] : null,
            'label' => isset($data['label']) ? $data['label'] : "",
            'shortName' => isset($data['shortName']) ? $data['shortName'] : "",
            'rawDataDirectoryPath' => isset($data['rawDataDirectoryPath']) ? $data['rawDataDirectoryPath'] : "",
            'active' => isset($data['active']) ? $data['active'] : 0,
            'isCensus' => isset($data['isCensus']) ? $data['isCensus'] : 0,
            // 'isLive' => isset($data['isLive']) ? $data['isLive'] : 0
        ];

        try {
            $company = $this->model->create($companyData);

            if (!$company && !empty($company->errors['messages'])) {
                return false;
            }

            if (isset($data['carrierId']) && count($data['carrierId']) >= 1) {
                for ($x = 0; $x < count($data['carrierId']); $x++) {
                    if (!empty($data['carrierId'][$x])) {
                        $carrierPAN = !empty($data['carrierPAN'][$x]) ? trim($data['carrierPAN'][$x]) : null;
                        $company->carriers()->attach(
                            $company->id,
                            [
                                'carrierId' => (int)$data['carrierId'][$x],
                                'billingAccountNumber' => trim($data['carrierBAN'][$x]),
                                'parentAccountNumber' => $carrierPAN,
                            ]
                        );
                        $company->save();
                    }
                }
            }

            /*$pooled = 0;

            if (isset($data['poolGroupId']) && count($data['poolGroupId']) >= 1) {
                for ($x = 0; $x < count($data['poolGroupId']); $x++) {
                    if (!empty($data['poolGroupId'][$x])) {
                        $base_cost = Money::fromString((string)$data['baseCost'][$x], new Currency('USD'))->getAmount();
                        $poolBAN = !empty($data['poolBAN'][$x]) ? trim($data['poolBAN'][$x]) : null;
                        $company->pools()->attach(
                            $company->id,
                            [
                                'poolGroupId' => (int)$data['poolGroupId'][$x],
                                'baseCost' => $base_cost,
                                'billingAccountNumber' => $poolBAN
                            ]
                        );
                        $company->save();
                        $pooled = 1;
                    }
                }

            }

            if (!$pooled) {
                //Set an entry with base cost -1 to turn off pooling
                $company->pools()->attach(
                    $company->id,
                    [
                        'poolGroupId' => 1,
                        'baseCost' => -1,
                    ]
                );
                $company->save();
            }*/


            //Pooling turned off by default for prospect companies

            /*( if ($company->isLive == 0) {

                 $pool_base = app()->make('WA\DataStore\PoolBase');
                 $table_name = !empty($pool_base) ? $pool_base->getTable() : pool_bases;
                 $data = [
                     "baseCost" => -1,
                     "companyId" => $company->id,
                     "poolGroupId" => 1
                 ];

                 \DB::table($table_name)->insert($data);
             }*/


            return $company;

        } catch (\Exception $e) {
            Log::error('[ ' . get_class() . ' ] | There was an issue: ' . $e->getMessage());
        }

    }

    /**
     * Delete a Company.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true)
    {
        if (!$this->model->find($id)) {
            return false;
        }

        if (!$soft) {
            $this->model->forceDelete($id);
        }

        return $this->model->destroy($id);
    }

    /**
     * Update a company.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {

        $company = $this->model->find($data['id']);

        if (!$company) {
            return false;
        }

        $company->name = $data['name'];
        $company->label = $data['label'];
        $company->shortName = $data['shortName'];
        $company->active = isset($data['active']) ? $data['active'] : 0;
        // $company->isLive = isset($data['isLive']) ? $data['isLive'] : 0;
        $company->isCensus = isset($data['isCensus']) ? $data['isCensus'] : 0;

        if (!$company->save()) {
            return false;
        }

        //Remove existing entries first to avoid duplicate rows
        $company->carriers()->detach();
        if (!empty($data['carrierId']) && count($data['carrierId']) >= 1) {
            for ($x = 0; $x < count($data['carrierId']); $x++) {
                if (!empty($data['carrierId'][$x])) {
                    $carrierPAN = !empty($data['carrierPAN'][$x]) ? trim($data['carrierPAN'][$x]) : null;
                    $company->carriers()->attach(
                        $company->id,
                        [
                            'carrierId' => (int)$data['carrierId'][$x],
                            'billingAccountNumber' => trim($data['carrierBAN'][$x]),
                            'parentAccountNumber' => $carrierPAN,
                        ]
                    );
                    $company->save();
                }
            }

        }

        //Remove existing entries first to avoid duplicate rows
       /* $company->pools()->detach();
        $pooled = 0;
        if (count($data['poolGroupId']) >= 1) {
            for ($x = 0; $x < count($data['poolGroupId']); $x++) {
                if (!empty($data['poolGroupId'][$x])) {
                    $base_cost = Money::fromString((string)$data['baseCost'][$x], new Currency('USD'))->getAmount();
                    $poolBAN = !empty($data['poolBAN'][$x]) ? trim($data['poolBAN'][$x]) : null;
                    $company->pools()->attach(
                        $company->id,
                        [
                            'poolGroupId' => (int)$data['poolGroupId'][$x],
                            'baseCost' => $base_cost,
                            'billingAccountNumber' => $poolBAN
                        ]
                    );
                    $company->save();
                    $pooled = 1;
                }
            }
        }

        if (!$pooled) {
            //Set an entry with base cost -1 to turn off pooling
            $company->pools()->attach(
                $company->id,
                [
                    'poolGroupId' => 1,
                    'baseCost' => -1,
                ]
            );
            $company->save();
        }*/


        return $company;

    }

    /**
     * Get Pool Bases by Company Id
     *
     * @param $id
     *
     * @return mixed
     */
    public function getPools($id)
    {
        $pool_base = app()->make('WA\DataStore\PoolBase');

        return $pool_base->where('companyId', $id)->get();
    }

    /**
     * Get Company Specific Carriers
     *
     * @param $id
     *
     * @return mixed
     */
    public function getCompanySpecific($id)
    {
        $company_carrier = app()->make('WA\DataStore\CompanyCarrier');

        return $company_carrier->where('companyId', $id)->get();
    }

    /**
     * Get company domains by the ID
     * if no id is defined it gets all
     *
     * @param int|null $companyId
     *
     * @return array of company domains
     */
    public function getDomains($companyId = null)
    {
        $companies_table = $this->model->getTable();
        $fields = [
            $this->domainsTable . '.domain',
            $companies_table . '.id',
            $companies_table . '.name'//,
            //$companies_table . '.externalId'
        ];

        $model = \DB::table($companies_table)
            ->join($this->domainsTable, $companies_table . '.id', '=', $this->domainsTable . '.companyId')
            ->where($this->domainsTable . '.active', 1);

        if (!empty($companyId)) {
            $model->where($companies_table . '.id', $companyId);
        }
        $sql = $model->get($fields);
        return $sql;
    }


    /**
     * Gets the Id of a company by the email
     * (does a best guess based on the allowed domain, returns a 0 is no match)
     *
     * @param string $email
     *
     * @return int
     */
    public function getIdByUserEmail($email)
    {
        $all_company_domains = $this->getDomains();
        $domain = explode('@', $email)[1];
        $match = $this->searchArrayObject($domain,'domain',$all_company_domains);

        if (!isset($match->id)) {
            return 0;
        }

        return $match->id;
    }


    /**
     * @param $value
     * @param $key
     * @param $array
     *
     * @return int|null|string
     */
    private function searchArrayObject($value, $key, $array) {
        foreach ($array as $k => $val) {
            if ($val->$key == $value) {
                return $val;
            }
        }
        return null;
    }
}
