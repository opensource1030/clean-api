<?php

namespace WA\DataStore\Modification;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Modification\ModificationTransformer;

/**
 * Class Modification
 *
 * @package WA\DataStore\Modification
 */
class Modification extends BaseDataStore
{
    protected  $table = 'modifications';

    protected $fillable = [
            'type',
            'value',
            'updated_at'];


    /**
     * Get all the owners for the modifications
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
  public function owner()
  {
      return $this->morphTo();
  }

    /**
     * Get the transformer instance
     *
     * @return ModificationTransformer
     */
    public function getTransformer()
    {
        return new OrderTransformer();
    }

}