<?php

namespace WA\Repositories\Device;

use Illuminate\Database\Eloquent\Model;
use Log;
use Schema;
use WA\Repositories\AbstractRepository;
use WA\Repositories\JobStatus\JobStatusInterface as StatusInterface;
use WA\Repositories\Traits\AttributableMethods;

class EloquentDevice extends AbstractRepository implements DeviceInterface
{
    use AttributableMethods;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var StatusInterface
     */
    protected $status;

    /**
     * @param Model $model
     * @param StatusInterface $status
     */
    public function __construct(Model $model, StatusInterface $status)
    {
        $this->model = $model;
        $this->status = $status;
    }

    /**
     * Get the device information by their employee.
     *
     * @param $userId (using CompanyID for now)
     *
     * @return Object object of device information by the employee
     */
    public function byUser($userId)
    {
        $userDevices =
            $this->model->with('users', function ($q) use ($userId) {
                $q->where('employeeId', $userId);
            })->get();

        if (!$userDevices) {
            return;
        }

        return $userDevices;
    }

    /**
     * Get device by their identification.
     *
     * @param $identification
     *
     * @return Object object of device information
     */
    public function byIdentification($identification)
    {
        return $this->model
            ->where('identification', $identification)
            ->first();
    }

    /**
     * Get the devices that are pending review ( that have not yet being assigned users).
     *
     * @param bool $all
     *
     * @return Object object of device information, for unassigned
     */
    public function byUnassigned($all = true)
    {
        $statusId = $this->status->idByName('Pending Review');

        $model = $this->model->where('statusId', $statusId);

        if (!$all) {
            return $model->first();
        }

        return $model->get();
    }

    /**
     * Sync an asset to devices.
     *
     * @param int $id of the device
     * @param array $ids of the assets to sync device with
     *
     * @return bool
     */
    public function syncAsset($id, array $ids)
    {
        $device = $this->byId($id);

        if (!$device) {
            Log::error('[' . get_class() . '] | Cannot sync the find the devices');

            return false;
        }

        try {
            $device->assets()->detach($ids['old']);
            $device->assets()->sync($ids['new']);

            return true;
        } catch (\Exception $e) {
            Log::error('Syncing the asset to device ' . $id . 'failed' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get all the unique identifications.
     *
     * @param array $exclude
     *
     * @return array of identification
     */
    public function getUniqueIdentification(array $exclude = [])
    {
        $model = $this->model;

        if ((bool)count($exclude)) {
            $model->whereNotIn('identification', $exclude);
        }

        $response = $model->groupBy('identification')
            ->get(['identification'])->toArray();

        return $response;
    }

    /**
     * Get companies devices.
     *
     * @param $id
     *
     * @return Builder/Query
     */
    public function byCompany($id)
    {
        return $this->model->whereHas('companies', function ($q) use ($id) {
            $q->where('companyId', $id);
        });
    }

    /**
     * Get the count of all unique types in the system.
     *
     * @param string $lastUpdated
     *
     * @return int of count
     */
    public function getCount($lastUpdated = null)
    {
        $model = $this->model;
        $created_column_name = 'created_at';

        if (!is_null($lastUpdated) && Schema::table($this->model->getTable(), $created_column_name)) {
            $model->where($created_column_name, $lastUpdated);
        }

        return (int)$model->count();
    }

    /**
     * Get the maximum value of the external ID.
     *
     * @return int
     */
    public function getMaxExternalId()
    {
        $externalIdColumnName = 'externalId';

        return (int)$this->model->max($externalIdColumnName);
    }

    public function getDataTable()
    {

        $query = $this->model->join('device_types', 'devices.deviceTypeId', '=', 'device_types.id');
        return $query;
    }


    /**
     * Get the model's transformation.
     */
    public function getTransformer()
    {
        return $this->model->getTransformer();
    }
}