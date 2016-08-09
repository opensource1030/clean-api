<?php

namespace WA\DataStore\Page;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Page\PageTransformer;

/**
 * Class Page
 *
 * @package WA\DataStore\Page
 * @property-read \WA\DataStore\Company\Company $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\User\User[] $users
 * @mixin \Eloquent
 */
class Page extends BaseDataStore
{
    protected  $table = 'pages';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('WA\DataStore\User\User', 'employees_pages', 'pageId', 'employeeId');
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