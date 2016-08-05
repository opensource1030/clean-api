<?php


namespace WA\DataStore;

use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletingTrait;

/**
 * Class Upload.
 */
class Upload extends BaseDataStore
{
    protected $fillable = ['uploadable_type', 'uploadable_id', 'fileName'];

    // Soft-deletes via trait
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function censuses()
    {
        return $this->morphedByMany('WA\DataStore\Census', 'uploadable');
    }
}
