<?php

namespace WA\DataStore\Page;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Page\PageTransformer;

/**
 * Class Page
 *
 * @package WA\DataStore\Page
 */
class Page extends BaseDataStore
{
    protected  $table = 'pages';

    protected $fillable = array('title', 'section', 'content', 'active', 'owner_type', 'owner_id');


    /**
     * Get all the owners for the page
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
     * @return PageTransformer
     */
    public function getTransformer()
    {
        return new PageTransformer();
    }

}
