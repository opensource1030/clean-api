<?php

namespace WA\DataStore;

/**
 * Class SyncJob.
 */
class SyncJob extends BaseDataStore
{
    protected $fillable = [
        'name',
        'statusId',
        'notes',
    ];

    protected $timeStamps = true;

    protected $table = 'sync_jobs';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('WA\DataStore\JobStatus', 'statusId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets()
    {
        return $this->hasMany('WA\DataStore\Asset\Asset', 'syncId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany('WA\DataStore\Device\Device', 'syncId');
    }
}
