<?php


namespace WA\Repositories\HelpDesk;

use WA\Services\Cache\CacheInterface;

class HelpDeskCacheDecorator extends HelpDeskDecorator
{

    /**
     * @var \WA\Services\Cache\CacheInterface
     */
    protected $cache;

    /**
     * @param HelpDeskInterface $helpDesk
     * @param CacheInterface    $cache
     */
    public function __construct(HelpDeskInterface $helpDesk, CacheInterface $cache)
    {

        parent::__construct($helpDesk);
        $this->cache = $cache;
    }

    /**
     * Generate a cache key
     *
     * @param $slug
     * @param $name
     *
     * @return string
     */
    protected function makeKey(
        $slug,
        $name
    ) {
        return md5($slug . '.' . $name);
    }

    /**
     * Get the count of asset
     * by default, it gets all once that do not exists in CLEAN
     *
     * @return int count of assets
     */
    public function getAssetCount()
    {
        return $this->helpDesk->getAssetCount();
    }

    /**
     * Get all the devices
     *
     * by default, it gets all once that do not exists in CLEAN
     *
     * @return int
     */
    public function getDeviceCount()
    {
        return $this->helpDesk->getDeviceCount();
    }

    /**
     * Get all assets
     * by default, it gets all once that do not exists in CLEAN
     *
     * @return array of assets
     */
    public function getAssets()
    {
        return $this->helpDesk->getAssets();
    }

    /**
     * Get all devices
     *
     * @return array devices
     */
    public function getDevices()
    {
        return $this->helpDesk->getDevices();
    }

    /**
     * Get all the devices linked to Assets,
     * by default it gets only un-synced links
     *
     * @param bool $pending
     *
     * @return array of linked assets to devices
     */
    public function getLinkedAssetDevices($pending = true)
    {
        return $this->helpDesk->getLinkedAssetDevices($spending = true);
    }

    /**
     * Get asset by identification
     *
     * @param string $identification
     *
     * @return array
     */
    public function assetByIdentification($identification)
    {

        $key = $this->makeKey('assetIdentity', $identification);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $asset = $this->helpDesk->assetByIdentification($identification);
        $this->cache->put($key, $asset);

        return $asset;
    }

    /**
     * Get the asset changelog
     *
     * @param bool $pending
     *
     * @return array of change logs
     */
    public function assetChangeLog($pending = true)
    {
        return $this->helpDesk->assetChangeLog($pending = true);
    }


    /**
     * Get device by identification
     *
     * @param string $identification
     *
     * @return array of device
     */
    public function deviceByIdentification($identification)
    {
        $key = $this->makeKey('deviceIdentity', $identification);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $device = $this->helpDesk->deviceByIdentification($identification);
        $this->cache->put($key, $device);

        return $device;

    }

    /**
     * Get an employee by name
     *
     * @param string $name
     *
     * @return array name information
     */
    public function employeeByName($name)
    {

        $key = $this->makeKey('employeeByName', $name);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->helpDesk->employeeByName($name);
        $this->cache->put($key, $employee);

        return $employee;

    }

    /**
     * Get all validators by their companyID
     *
     * @param int $companyId
     *
     * @return Object object of validators
     */
    public function getValidators($companyId)
    {

        $key = $this->makeKey('companyValidators', $companyId);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $validators = $this->helpDesk->getValidators($companyId);
        $this->cache->put($key, $validators);

        return $validators;
    }

    /**
     * Get all supervisors by their companyId (Sups?)
     *
     * @param int $companyId
     *
     * @return Object object of supervisors/managers
     */
    public function getSupervisors($companyId)
    {

        $key = $this->makeKey('companySupervisors', $companyId);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $supervisors = $this->helpDesk->getSupervisors($companyId);
        $this->cache->put($key, $supervisors);

        return $supervisors;
    }

    /**
     * Get an employee by the identification
     *
     * @param string $identification
     *
     * @return string of identification
     */
    public function employeeByIdentification($identification)
    {

        $key = $this->makeKey('employeeById', $identification);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->helpDesk->employeeByIdentification($identification);
        $this->cache->put($key, $employee);

        return $employee;
    }

    /**
     * Get the departmental path
     *
     * @param string $path
     *
     * @return int id of the path
     */
    public function getDepartmentId($path)
    {

        $key = $this->makeKey('departmentId', $path);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $departmentId = $this->helpDesk->getDepartmentId($path);
        $this->cache->put($key, $departmentId);

        return $departmentId;


    }

    /**
     * Get EasyVista Id based on CLEAN ID
     *
     * @param $id
     * @return int $id
     */
    public function getEmployeeExternalId($id)
    {
        return $this->helpDesk->getEmployeeExternalId($id);
    }

    /**
     * Get the count of department paths that are not currently synced with CLEAN from Easy Vista.
     *
     */
    public function getDeptPathCount()
    {
        return $this->helpDesk->getDeptPathCount();
    }

    /**
     * Get department paths that are not currently synced with CLEAN from Easy Vista.
     *
     */
    public function getDeptPath()
    {
        return $this->helpDesk->getDeptPath();
    }
}