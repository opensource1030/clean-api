<?php


namespace WA\DataStore;

use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletingTrait;
use WA\DataStore\Traits\BelongsToJobStatus;

/**
 * Class Census.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Upload[] $uploads
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Employee\Employee[] $employees
 * @property-read \WA\DataStore\Company\Company $company
 * @property-read \WA\DataStore\JobStatus $status
 * @property-read \WA\DataStore\JobStatus $jobstatus
 * @mixin \Eloquent
 */
class Census extends BaseDataStore
{
    protected $table = 'censuses';

    protected $fillable = ['companyId', 'statusId', 'rawFileLineCount', 'processLineCount', 'file'];

    // Soft-deletes via trait
    use SoftDeletingTrait;
    use BelongsToJobStatus;

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany|Overrides\MorphToMany
     */
    public function uploads()
    {
        return $this->morphToMany('WA\DataStore\Upload', 'uploadable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany('WA\DataStore\Employee\Employee', 'syncId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('WA\DataStore\JobStatus', 'statusId');
    }
    
}
