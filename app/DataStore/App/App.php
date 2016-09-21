<?php

namespace WA\DataStore\App;

use WA\DataStore\BaseDataStore;
use WA\DataStore\App\AppTransformer;

/**
 * Class App
 *
 * @package WA\DataStore\App
 */
class App extends BaseDataStore
{
    protected  $table = 'apps';

    protected $fillable = [
            'type',
            'image',
            'description',
            'updated_at'];

    /**
     * Get all the owners for the app
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
     * @return AppTransformer
     */
    public function getTransformer()
    {
        return new AppTransformer();
    }
}