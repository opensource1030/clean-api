<?php

namespace WA\Services\Form\Asset;

use Log;
use WA\Repositories\Asset\AssetInterface;
use WA\Repositories\JobStatus\JobStatusInterface;
use WA\Services\Form\AbstractForm;

/**
 * Class AssetForm.
 */
class AssetForm extends AbstractForm
{
    /**
     * @var AssetInterface
     */
    protected $asset;

    /**
     * @var JobStatusInterface
     */
    protected $jobStatus;

    /**
     * @var AssetFormValidator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $notifyContainer = 'asset';

    /**
     * @param AssetInterface     $asset
     * @param JobStatusInterface $jobStatus
     * @param AssetFormValidator $validator
     */
    public function __construct(AssetInterface $asset, JobStatusInterface $jobStatus, AssetFormValidator $validator)
    {
        $this->asset = $asset;
        $this->jobStatus = $jobStatus;
        $this->validator = $validator;
    }

    /**
     * Get an individual asset.
     *
     * @param $id
     *
     * @return Object of asset | null
     */
    public function getAsset($id)
    {
        $asset = null;

        try {
            $asset = $this->asset->byId($id);
        } catch (\Exception $e) {
            $this->notify('error', 'Failed to get Asset Id #'.$id.'');
        }

        return $asset;
    }

    /**
     * @return bool|Object
     */
    public function getPendingDatatable()
    {
        try {
            return $this->asset->byUnassigned();
        } catch (\Exception $e) {
            // Log for now
            Log::error('Something went wrong, '.$e->getMessage());
        }

        return false;
    }

    /**
     * Update am asset with the respective params.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        if (!$this->valid($data)) {
            $this->errors = $this->validator->errors();
            $this->notify('error', 'There is an issue with some of the data, please try again');

            return false;
        }

        if (!$this->asset->update($data)) {
            $this->notify('error', 'Could not update the asset at this time, try again later');

            return false;
        }

        return true;
    }

    /**
     * Get all unassigned assets.
     *
     * @param bool $all
     *
     * @return Objects objects | Object of assets
     */
    public function getUnassigned($all = true)
    {
        $unassigned = null;

        try {
            $unassigned = $this->asset->byUnassigned($all);
        } catch (\PDOException $e) {
            //  just log for now
            Log::error('[ '.get_class().'] failed to get object '.$e->getMessage());
        }

        return $unassigned;
    }

    /**
     * Remove an attached employee from an asset.
     *
     * @param array $data
     *
     * @return bool
     */
    public function detachAsset(array  $data)
    {
        if (!$this->asset->detachByEmployee($data['currAssignEmployeeId'], $data['id'])) {
            $this->notify('error', 'Could not un-assign that employee, please try again later');

            return false;
        }

        return true;
    }
}
