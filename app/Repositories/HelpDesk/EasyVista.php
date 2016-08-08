<?php

namespace WA\Repositories\HelpDesk;

use DB;
use Illuminate\Database\Eloquent\Model;
use Log;
use WA\Exceptions\Repositories\HelpDesk\EasyVistaFailedConnectionException;
use WA\Repositories\Asset\AssetInterface;
use WA\Repositories\Device\DeviceInterface;
use WA\Repositories\Employee\EmployeeInterface;
use WA\Repositories\SyncJob\SyncJobInterface;

/**
 * A Repository for interacting with EasyVista's System.
 *
 * Class EasyVista
 */
class EasyVista implements HelpDeskInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \WA\Repositories\Asset\AssetInterface
     */
    protected $asset;

    /**
     * @var \WA\Repositories\Device\DeviceInterface
     */
    protected $device;

    /**
     * @var SyncJobInterface
     */
    protected $sync;

    protected $cached = [
        'devices' => [
            'isPendingSync' => true,
            'existingIds'   => [],
        ],
        'assets'  => [
            'isPendingSync' => true,
            'existingIds'   => [],
        ],
        'cxn'     => null,
    ];

    /**
     * Time that a last sync was done.
     *
     * @var string
     */
    protected $lastSyncTime;

    /**
     * @var array syncable collection
     */
    protected $syncable = [
        'assets',
        'devices',
    ];

    protected $easyVistaCatalogIds = [
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12,
        13,
        14,
    ];

    /**
     * @param Model $model
     * @param AssetInterface $asset
     * @param DeviceInterface $device
     * @param SyncJobinterface $sync
     * @param EmployeeInterface $employee
     */
    public function __construct(
        Model $model,
        AssetInterface $asset,
        DeviceInterface $device,
        SyncJobInterface $sync,
        EmployeeInterface $employee
    ) {
        $this->model = $model;
        $this->asset = $asset;
        $this->device = $device;
        $this->sync = $sync;
        $this->lastSyncTime = $sync->getLastSyncTime('help-desk');
        $this->employee = $employee;

//        $this->cached['cxn'] = DB::connection('easyvista');
    }

    /**
     * Get the count of assets that are not currently synced with CLEAN from Easy Vista.
     *
     * @param string the last updated datetime
     *
     * @return int count of assets
     */
    public function getAssetCount($lastUpdated = null)
    {
        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');

            $easy_vista_categories = implode(',', $this->easyVistaCatalogIds);
            $max_id = $this->asset->getMaxExternalId();

            $query = '
            SELECT
                COUNT(MOBILE_NUMBER.ASSET_ID) [count]

            FROM AM_ASSET AS MOBILE_NUMBER

            LEFT JOIN AM_EMPLOYEE USERS ON MOBILE_NUMBER.EMPLOYEE_ID = USERS.EMPLOYEE_ID
            LEFT JOIN AM_STATUS ON AM_STATUS.STATUS_ID = MOBILE_NUMBER.STATUS_ID
            LEFT JOIN AM_DOMAIN ON USERS.DEFAULT_DOMAIN_ID = AM_DOMAIN.DOMAIN_ID
            LEFT JOIN AM_DEPARTMENT_PATH USER_DEPT ON USERS.DEPARTMENT_ID = USER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_DEPARTMENT_PATH NUMBER_DEPT ON MOBILE_NUMBER.DEPARTMENT_ID = NUMBER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_SUPPLIER ON MOBILE_NUMBER.SUPPLIER_ID = AM_SUPPLIER.SUPPLIER_ID
            LEFT JOIN AM_EMPLOYEE MANAGER ON  USERS.MANAGER_ID = MANAGER.EMPLOYEE_ID
            LEFT JOIN AM_ASSET_LINKS ON MOBILE_NUMBER.ASSET_ID = AM_ASSET_LINKS.PARENT_ASSET_ID

            ';

            if (!is_null($lastUpdated)) {
                $query .= " WHERE MOBILE_NUMBER.LAST_UPDATE > '$this->lastSyncTime' AND MOBILE_NUMBER.CATALOG_ID IN($easy_vista_categories) AND MOBILE_NUMBER.ASSET_TAG <> '' AND MOBILE_NUMBER.ASSET_TAG <> '0'";
            } else {
                $query .= " WHERE MOBILE_NUMBER.ASSET_ID > $max_id AND MOBILE_NUMBER.CATALOG_ID IN($easy_vista_categories) AND MOBILE_NUMBER.ASSET_TAG <> '' AND MOBILE_NUMBER.ASSET_TAG <> '0'";
            }

            $evCount = (int)$cxn->select(DB::Raw($query))[0]->count;
//            $sysCount = $this->asset->getCount();

            $this->cached['assets']['isPendingSync'] = (bool)$evCount;

            return $evCount;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }

    /**
     * Get the count of devices that are not currently synced with CLEAN from Easy Vista.
     *
     *
     * @param $lastUpdated string datetime
     *
     * @return int
     */
    public function getDeviceCount($lastUpdated = null)
    {
        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');
            $easy_vista_categories = implode(',', $this->easyVistaCatalogIds);

            $max_id = $this->device->getMaxExternalId();

            $query = '

           SELECT
             COUNT(DEVICE.ASSET_ID) [count]

            FROM
              AM_ASSET AS DEVICE

            LEFT JOIN AM_EMPLOYEE AS [USERS] ON DEVICE.EMPLOYEE_ID = USERS.EMPLOYEE_ID
            LEFT JOIN AM_DOMAIN ON USERS.DEFAULT_DOMAIN_ID = AM_DOMAIN.DOMAIN_ID
            LEFT JOIN AM_DEPARTMENT_PATH USER_DEPT ON USERS.DEPARTMENT_ID = USER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_SUPPLIER ON DEVICE.SUPPLIER_ID = AM_SUPPLIER.SUPPLIER_ID
            LEFT JOIN AM_EMPLOYEE MANAGER ON USERS.MANAGER_ID = MANAGER.EMPLOYEE_ID
            LEFT JOIN AM_CATALOG ON DEVICE.CATALOG_ID = AM_CATALOG.CATALOG_ID
            LEFT JOIN AM_MANUFACTURER ON AM_CATALOG.MANUFACTURER_ID = AM_MANUFACTURER.MANUFACTURER_ID
            LEFT JOIN AM_UN_CLASSIFICATION ON AM_CATALOG.UN_CLASSIFICATION_ID = AM_UN_CLASSIFICATION.UN_CLASSIFICATION_ID
            ';

            if (!is_null($lastUpdated)) {
                $query .= " WHERE DEVICE.LAST_UPDATE > $lastUpdated' AND t1.CATALOG_ID IN ($easy_vista_categories) AND DEVICE.ASSET_TAG <> '' AND DEVICE.ASSET_TAG <> '0'";
            } else {
                $query .= " WHERE DEVICE.ASSET_ID > $max_id AND DEVICE.CATALOG_ID NOT IN ($easy_vista_categories) AND DEVICE.ASSET_TAG <> '' AND DEVICE.ASSET_TAG <> '0'";
            }

            $evCount = (int)$cxn->select(DB::Raw($query))[0]->count;

//            $sysCount = $this->device->getCount();

            $this->cached['devices']['isPendingSync'] = (bool)$evCount;

            return $evCount;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get all the assets that have not being synced from easyvista.
     *
     * @param $lastUpdated string
     *
     * @return array object of assets
     */
    public function getAssets($lastUpdated = null)
    {
        try {

            // exist early if we're trying to get pending and existingIds we have no syncs
            if ($this->cached['assets']['isPendingSync'] === false) {
                return [];
            }

            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');
            $easy_vista_categories = implode(',', $this->easyVistaCatalogIds);
            $max_id = $this->asset->getMaxExternalId();

            $query = '
            SELECT
                MOBILE_NUMBER.ASSET_ID AS EV_MOBILE_NUMBER_ID,
                MOBILE_NUMBER.ASSET_TAG AS EV_MOBILE_NUMBER,
                AM_SUPPLIER.SUPPLIER AS EV_CARRIER,
                AM_STATUS.STATUS_EN AS EV_LINE_STATUS,
                MOBILE_NUMBER.E_SIM_CARD_NUMBER AS EV_SIM_CARD_NUMBER,
                MOBILE_NUMBER.ENTRY_DATE AS EV_START_DATE,
                MOBILE_NUMBER.SCHEDULED_END AS EV_UPGRADE_DATE,
                MOBILE_NUMBER.REMOVED_DATE AS EV_END_DATE,
                MOBILE_NUMBER.MONTH_DURATION AS EV_DURATION,
                MOBILE_NUMBER.E_ETF AS EV_ETF,
                MOBILE_NUMBER.COMMENT_ASSET AS EV_MOBILE_NUMBER_COMMENTS,
                USERS.IDENTIFICATION AS EV_CLEAN_ID,
                AM_DOMAIN.NAME_EN AS EV_COMPANY

            FROM AM_ASSET AS MOBILE_NUMBER

            LEFT JOIN AM_EMPLOYEE USERS ON MOBILE_NUMBER.EMPLOYEE_ID = USERS.EMPLOYEE_ID
            LEFT JOIN AM_STATUS ON AM_STATUS.STATUS_ID = MOBILE_NUMBER.STATUS_ID
            LEFT JOIN AM_DOMAIN ON USERS.DEFAULT_DOMAIN_ID = AM_DOMAIN.DOMAIN_ID
            LEFT JOIN AM_DEPARTMENT_PATH USER_DEPT ON USERS.DEPARTMENT_ID = USER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_DEPARTMENT_PATH NUMBER_DEPT ON MOBILE_NUMBER.DEPARTMENT_ID = NUMBER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_SUPPLIER ON MOBILE_NUMBER.SUPPLIER_ID = AM_SUPPLIER.SUPPLIER_ID
            LEFT JOIN AM_EMPLOYEE MANAGER ON  USERS.MANAGER_ID = MANAGER.EMPLOYEE_ID
            LEFT JOIN AM_ASSET_LINKS ON MOBILE_NUMBER.ASSET_ID = AM_ASSET_LINKS.PARENT_ASSET_ID
            ';

            if (!is_null($lastUpdated)) {
                $query .= " WHERE MOBILE_NUMBER.LAST_UPDATE > '$this->lastSyncTime' AND MOBILE_NUMBER.CATALOG_ID IN($easy_vista_categories) AND MOBILE_NUMBER.ASSET_TAG <> '' AND MOBILE_NUMBER.ASSET_TAG <> '0'";
            } else {
                $query .= " WHERE MOBILE_NUMBER.ASSET_ID > $max_id AND MOBILE_NUMBER.CATALOG_ID IN($easy_vista_categories) AND MOBILE_NUMBER.ASSET_TAG <> '' AND MOBILE_NUMBER.ASSET_TAG <> '0'";
            }
            $assets = $cxn->select(DB::Raw($query));

            return $assets;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }

    /**
     * Get all devices from EasyVista not yet synced with CLEAN.
     *
     * @param $lastUpdated from the sync
     *
     * @return array of devices
     */
    public function getDevices($lastUpdated = null)
    {
        try {

            // exist early if we're trying to get pending and existingIds we have no syncs
            if ($this->cached['devices']['isPendingSync'] === false) {
                return [];
            }

            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');

            $easy_vista_categories = implode(',', $this->easyVistaCatalogIds);

            $max_id = $this->device->getMaxExternalId();

            $query = '

           SELECT
              USERS.IDENTIFICATION AS [EV_CLEAN_ID],
              AM_DOMAIN.NAME_EN AS [EV_COMPANY],
              AM_UN_CLASSIFICATION.UN_CLASSIFICATION_EN AS [EV_TYPE],
              DEVICE.ASSET_ID AS [EV_IMEI_MEID_SERIAL_NUMBER_ID],
              DEVICE.ASSET_TAG AS [EV_IMEI_ME/ID_SERIAL_NUMBER],
              AM_MANUFACTURER.MANUFACTURER AS [EV_MANUFACTURER],
              AM_CATALOG.ARTICLE_MODEL AS [EV_DEVICE_MODEL],
              DEVICE.END_OF_WARANTY AS [EV_WARRANTY_END_DATE],
              DEVICE.INSTALLATION_DATE AS [EV_ISSUE_DATE],
              DEVICE.COMMENT_ASSET AS [EV_DEVICE_COMMENTS]

            FROM
              AM_ASSET AS DEVICE

            LEFT JOIN AM_EMPLOYEE AS [USERS] ON DEVICE.EMPLOYEE_ID = USERS.EMPLOYEE_ID
            LEFT JOIN AM_DOMAIN ON USERS.DEFAULT_DOMAIN_ID = AM_DOMAIN.DOMAIN_ID
            LEFT JOIN AM_DEPARTMENT_PATH USER_DEPT ON USERS.DEPARTMENT_ID = USER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_SUPPLIER ON DEVICE.SUPPLIER_ID = AM_SUPPLIER.SUPPLIER_ID
            LEFT JOIN AM_EMPLOYEE MANAGER ON USERS.MANAGER_ID = MANAGER.EMPLOYEE_ID
            LEFT JOIN AM_CATALOG ON DEVICE.CATALOG_ID = AM_CATALOG.CATALOG_ID
            LEFT JOIN AM_MANUFACTURER ON AM_CATALOG.MANUFACTURER_ID = AM_MANUFACTURER.MANUFACTURER_ID
            LEFT JOIN AM_UN_CLASSIFICATION ON AM_CATALOG.UN_CLASSIFICATION_ID = AM_UN_CLASSIFICATION.UN_CLASSIFICATION_ID
            ';

            if (!is_null($lastUpdated)) {
                $query .= " WHERE DEVICE.LAST_UPDATE > $lastUpdated' AND DEVICE.CATALOG_ID IN ($easy_vista_categories) AND DEVICE.ASSET_TAG <> '' AND DEVICE.ASSET_TAG <> '0'";
            } else {
                $query .= " WHERE DEVICE.ASSET_ID > $max_id AND DEVICE.CATALOG_ID NOT IN ($easy_vista_categories) AND DEVICE.ASSET_TAG <> '' AND DEVICE.ASSET_TAG <> '0'";
            }

            $devices = $cxn->select(DB::Raw($query));

            return $devices;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }

    /**
     * Get EasyVista Id based on CLEAN ID
     *
     * @param $id
     * @return int $id
     */
    public function getEmployeeExternalId($id)
    {
        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');
            $query = <<<BLOCK
            SELECT
            EMPLOYEE_ID as Id
            FROM AM_EMPLOYEE WHERE IDENTIFICATION = '$id';
BLOCK;

            $externalId = $cxn->select(DB::Raw($query));
            return $externalId;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }


    /**
     * Get asset by identification.
     *
     * @param string $identification
     *
     * @return array
     */
    public function assetByIdentification($identification)
    {
        $this->model->setTable('AM_ASSET');

        try {
            $response = $this->model->where('ASSET_TAG', $identification)
                ->whereIn('CATALOG_ID', $this->easyVistaCatalogIds)
                ->get()->toArray();

            return $response;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('An error happened will getting asset by identification: ' . $e->getMessage());
        }
    }

    /**
     * Get device by identification.
     *
     * @param string $identification
     *
     * @return array of device
     */
    public function deviceByIdentification($identification)
    {
        $this->model->setTable('AM_ASSET');

        try {
            $response = $this->model->where('ASSET_TAG', $identification)
                ->whereNotIn('CATALOG_ID', $this->easyVistaCatalogIds)
                ->get()->toArray();

            return $response;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('An error happened will getting device by identification: ' . $e->getMessage());
        }
    }

    /**
     * Get an employee by name.
     *
     * @param string $name
     *
     * @return array name information
     */
    public function employeeByName($name)
    {
        $this->model->setTable('AM_EMPLOYEE');

        try {
            $response = $this->model->whereRaw('LAST_NAME LIKE ?', ["%$name%"])
                ->get()->toArray();

            return $response;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Failed to get name' . $e->getMessage());
        }
    }

    /**
     * Get all the devices linked to Assets.
     *
     * @param bool $pending
     *
     * @return array of linked assets to devices
     */
    public function getLinkedAssetDevices($pending = true)
    {

        // only sync when there are newer links
        // $existingAssetCount = count($assets = $this->asset->getUniqueIdentification());

        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');

            $query = <<<Q

            SELECT
                MOBILE_NUMBER.ASSET_TAG AS EV_MOBILE_NUMBER,
                MOBILE_NUMBER.ASSET_ID AS EV_MOBILE_NUMBER_ID,
                EQUIPMENT.ASSET_TAG AS EV_IMEI_MEID_SERIAL_NUMBER,
                EQUIPMENT.ASSET_ID AS EV_IMEI_MEID_SERIAL_NUMBER_ID

            FROM
                AM_ASSET_LINKS

            JOIN AM_ASSET MOBILE_NUMBER ON AM_ASSET_LINKS.PARENT_ASSET_ID = MOBILE_NUMBER.ASSET_ID
            JOIN AM_ASSET EQUIPMENT ON AM_ASSET_LINKS.ASSET_ID = EQUIPMENT.ASSET_ID

Q;

            $response = $cxn->select(DB::Raw($query));

            return $response;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Cannot get links, see: ' . $e->getMessage());
        }
    }

    /**
     * Get all employees linked to assets
     */
//    public function getLinkedAssetEmployees($pending = true)
//    {
//
//        // only sync when there are newer links
//        // $existingAssetCount = count($assets = $this->asset->getUniqueIdentification());
//
//        try {
//            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');
//
//            $query = <<<Q
//
//            SELECT
//              MOBILE_NUMBER.ASSET_TAG AS EV_MOBILE_NUMBER,
//              USERS.IDENTIFICATION AS EV_CLEAN_ID
//
//            FROM AM_ASSET AS MOBILE_NUMBER
//
//              LEFT JOIN AM_EMPLOYEE USERS ON MOBILE_NUMBER.EMPLOYEE_ID = USERS.EMPLOYEE_ID
//              LEFT JOIN AM_STATUS ON AM_STATUS.STATUS_ID = MOBILE_NUMBER.STATUS_ID
//              LEFT JOIN AM_DOMAIN ON USERS.DEFAULT_DOMAIN_ID = AM_DOMAIN.DOMAIN_ID
//              LEFT JOIN AM_DEPARTMENT_PATH USER_DEPT ON USERS.DEPARTMENT_ID = USER_DEPT.DEPARTMENT_ID
//              LEFT JOIN AM_DEPARTMENT_PATH NUMBER_DEPT ON MOBILE_NUMBER.DEPARTMENT_ID = NUMBER_DEPT.DEPARTMENT_ID
//              LEFT JOIN AM_SUPPLIER ON MOBILE_NUMBER.SUPPLIER_ID = AM_SUPPLIER.SUPPLIER_ID
//              LEFT JOIN AM_EMPLOYEE MANAGER ON  USERS.MANAGER_ID = MANAGER.EMPLOYEE_ID
//              LEFT JOIN AM_ASSET_LINKS ON MOBILE_NUMBER.ASSET_ID = AM_ASSET_LINKS.PARENT_ASSET_ID
//
//            WHERE USERS.IDENTIFICATION IS NOT NULL
//
//Q;
//
//            $response = $cxn->select(DB::Raw($query));
//
//            return $response;
//        } catch (EasyVistaFailedConnectionException $e) {
//            Log::error('Cannot get links, see: '.$e->getMessage());
//        }
//    }


    /**
     * Get the asset changelog.
     *
     * @param bool $pending
     *
     * @return array of change logs
     */
    public function assetChangeLog($pending = true)
    {
        $lastSynced = $this->sync->getLastSyncTime('help-desk');

        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');

            $query = <<<Q

            SELECT
                AM_HISTORY_PARAM.HISTORY_PARAM_LABEL_EN AS EV_CHANGE_TYPE,
                MOBILE_NUMBER.ASSET_ID AS EV_MOBILE_NUMBER_ID,
                MOBILE_NUMBER.ASSET_TAG AS EV_MOBILE_NUMBER,
                AM_HISTORY.OLD_VALUE_EN AS EV_OLD_VALUE,
                AM_HISTORY.NEW_VALUE_EN AS EV_NEW_VALUE,
                AM_HISTORY.CHANGE_DATE,
                AM_HISTORY_PARAM.TABLE_NAME as EV_TABLE_NAME,
				AM_HISTORY_PARAM.FIELD_NAME as EV_FIELD_NAME


            FROM
                AM_ASSET AS MOBILE_NUMBER

            LEFT JOIN AM_EMPLOYEE USERS ON MOBILE_NUMBER.EMPLOYEE_ID =  USERS.EMPLOYEE_ID
            LEFT JOIN AM_STATUS ON AM_STATUS.STATUS_ID =  MOBILE_NUMBER.STATUS_ID
            LEFT JOIN AM_DOMAIN ON USERS.DEFAULT_DOMAIN_ID =  AM_DOMAIN.DOMAIN_ID
            LEFT JOIN AM_DEPARTMENT_PATH USER_DEPT ON USERS.DEPARTMENT_ID = USER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_DEPARTMENT_PATH NUMBER_DEPT ON MOBILE_NUMBER.DEPARTMENT_ID =  NUMBER_DEPT.DEPARTMENT_ID
            LEFT JOIN AM_SUPPLIER ON MOBILE_NUMBER.SUPPLIER_ID =  AM_SUPPLIER.SUPPLIER_ID
            LEFT JOIN AM_EMPLOYEE MANAGER ON USERS.MANAGER_ID =  MANAGER.EMPLOYEE_ID
            LEFT JOIN AM_ASSET_LINKS ON MOBILE_NUMBER.ASSET_ID =  AM_ASSET_LINKS.PARENT_ASSET_ID
            LEFT JOIN AM_ASSET DEVICE ON AM_ASSET_LINKS.ASSET_ID =  DEVICE.ASSET_ID
            LEFT JOIN AM_CATALOG  ON DEVICE.CATALOG_ID = AM_CATALOG.CATALOG_ID
            LEFT JOIN AM_MANUFACTURER ON AM_CATALOG.MANUFACTURER_ID =  AM_MANUFACTURER.MANUFACTURER_ID
            LEFT JOIN AM_UN_CLASSIFICATION ON AM_CATALOG.UN_CLASSIFICATION_ID = AM_UN_CLASSIFICATION.UN_CLASSIFICATION_ID
            LEFT JOIN AM_HISTORY ON  MOBILE_NUMBER.ASSET_ID  =  AM_HISTORY.ID
            LEFT JOIN AM_HISTORY_PARAM ON AM_HISTORY.HISTORY_PARAM_ID =  AM_HISTORY_PARAM.HISTORY_PARAM_ID

            WHERE
                AM_HISTORY_PARAM.TRIGGER_TABLE_NAME IN ('AM_ASSET','AM_ASSET_LINKS')
                AND AM_HISTORY.CHANGE_DATE >= '$this->lastSyncTime'
Q;

            $response = $cxn->select(DB::Raw($query));

            return $response;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Cannot get change log');
        }
    }

    /**
     * Get all validators by their companyID.
     *
     * @param int $companyId
     *
     * @return array
     */
    public function getValidators($companyId = null)
    {
        if (is_null($companyId)) {
            return;
        }

        $this->model->setTable('AM_EMPLOYEE');

        try {
            $model = $this->model->select(
                'LAST_NAME AS name',
                'LOCATION_ID as locationId',
                'E_MAIL as email',
                'EMPLOYEE_ID as id'
            );

            $response = $model->where('APPROVED_TO_VALIDATE', 1)
                ->where('DEFAULT_DOMAIN_ID', $companyId)
                ->get()->toArray();

            return $response;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Cannot get validators ' . $e->getMessage());
        }
    }

    /**
     * Get all supervisors but their companayId (Sups?).
     *
     * @param int $companyId null
     *
     * @return array
     */
    public function getSupervisors($companyId = null)
    {
        if (is_null($companyId)) {
            return;
        }

        try {
            //Get the company name
            $company = app()->make('WA\Repositories\Company\CompanyInterface');
            $companyDetails = $company->byId($companyId);
            $companyName = $companyDetails->name;


            //Get the domain id for the company
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');
            $query = <<<Q
        SELECT DOMAIN_ID FROM AM_DOMAIN WHERE NAME_EN = '$companyName'
Q;
            $domainId = (int)$cxn->select(DB::Raw($query))[0]->DOMAIN_ID;

            if (is_null($domainId)) {
                return;
            }


            //Get the supervisors for the domain Id
            $this->model->setTable('AM_EMPLOYEE');
            $managersId = $this->model->select('MANAGER_ID')
                ->whereNotNull('MANAGER_ID')
                ->where('MANAGER_ID', '>', 0)
                ->where('DEFAULT_DOMAIN_ID', $domainId)
                ->groupBy('MANAGER_ID')
                ->get()->toArray();

            $model = $this->model->select(
                DB::Raw('MAX("LAST_NAME") as name'),
                DB::Raw('MAX("EMPLOYEE_ID") as id'),
                DB::Raw('MAX("E_MAIL") as email')
            )
                ->where('DEFAULT_DOMAIN_ID', $domainId)
                ->groupBy('EMPLOYEE_ID');

            // DR: Commented out due to PHP7 / PDO issue with large
            // lists of parameters.
            // @TODO: Resolve the larger PHP7/PDO issue
            //           $response = $model->whereIn('EMPLOYEE_ID', $managersId)->get()->toArray();
            $response = $model->get()->toArray();
            $managersId = array_flatten($managersId);

            foreach ($response as $elementKey => $element) {
                if (!in_array($element['id'], $managersId)) {
                    unset($response[$elementKey]);
                }
            }

            return $response;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Cannot get supervisors' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get an employee by the identification.
     *
     * @param string $identification
     *
     * @return string of identification
     */
    public function employeeByIdentification($identification)
    {
        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');

            $query = "
      SELECT IDENTIFICATION FROM AM_EMPLOYEE
      WHERE IDENTIFICATION = '$identification'
      ";

            $devices = $cxn->select(DB::Raw($query));

            return $devices;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }

    /**
     * Get the departmental path by the id.
     *
     * @param string $path
     *
     * @return int id of the path
     */
    public function getDepartmentId($path)
    {
        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');

            $query = "
        SELECT DEPARTMENT_ID
          FROM AM_DEPARTMENT_PATH
        WHERE DEPARTMENT_PATH_EN = '$path'
      ";

            $devices = $cxn->select(DB::Raw($query));

            return $devices;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }

    /**
     * Get the count of department paths that are not currently synced with CLEAN from Easy Vista.
     *
     *
     * @return int count of department paths
     */
    public function getDeptPathCount()
    {
        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');
            $udl_value_paths = app()->make('WA\Repositories\UdlValuePath\UdlValuePathInterface');
            $max_id = $udl_value_paths->getMaxExternalId();
            $max_id = $max_id[0]->externalId;

            $query = <<< BLOCK
            select count(DEPARTMENT_ID) [count] from AM_DEPARTMENT_PATH
where DEPARTMENT_ID >  $max_id
BLOCK;

            $deptCount = (int)$cxn->select(DB::Raw($query))[0]->count;

            $this->cached['departmentPath']['isPendingSync'] = (bool)$deptCount;

            return $deptCount;
        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }

    /**
     * Get department paths that are not currently synced with CLEAN from Easy Vista.
     *
     *
     * @return department paths
     */
    public function getDeptPath()
    {
        try {
            $cxn = $this->cached['cxn'] ?: DB::connection('easyvista');
            $udl_value_paths = app()->make('WA\Repositories\UdlValuePath\UdlValuePathInterface');
            $max_id = $udl_value_paths->getMaxExternalId();
            $max_id = $max_id[0]->externalId;


            $query = <<< BLOCK
select DEPARTMENT_ID, DEPARTMENT_PATH_EN from AM_DEPARTMENT_PATH
where DEPARTMENT_ID >  $max_id
BLOCK;

            $deptPaths = $cxn->select(DB::Raw($query));

            return $deptPaths;

        } catch (EasyVistaFailedConnectionException $e) {
            Log::error('Something failed on the connection, : ' . $e->getMessage());
        }
    }


}
