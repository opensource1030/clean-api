<?php

namespace WA\DataStore\Company;

use WA\DataStore\BaseDataStore;

/**
 * Class CompanyUserImportJob.
 *
 * @mixin \Eloquent
 */
class CompanyUserImportJob extends BaseDataStore
{
    const STATUS_PENDING = 0;
    const STATUS_WORKING = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;

    /**
     * make return packet
     *
     * @return mixed
     */
    public function getJobData() {
        $data = new \stdClass();
        $data->id = $this->id;
        $data->type = 'jobs';

        $attributes = new \stdClass();
        $attributes->status = $this->getStatusText();
        $attributes->total  = $this->total;
        $attributes->created = strtotime($this->created_at);
        $attributes->updated = strtotime($this->updated_at);
        $attributes->errors = 0;
        $attributes->sampleUser = unserialize($this->sample);
        $attributes->CSVfields = unserialize($this->fields);
        $attributes->mappings = unserialize($this->mappings);

        $data->attributes = $attributes;

        $result = new \stdClass();
        $result->data = $data;

        return json_encode($result);
    }

    /**
     * get status text
     *
     * @return string
     */
    public function getStatusText() {
        if($this->status == static::STATUS_PENDING) {
            return 'Pending';
        } else if($this->status == static::STATUS_WORKING) {
            return 'Working';
        } else if($this->status == static::STATUS_SUSPENDED) {
            return 'Suspended';
        } else if($this->status == static::STATUS_COMPLETED) {
            return 'Completed';
        } else if($this->status == static::STATUS_CANCELED) {
            return 'Canceled';
        }
    }
}
