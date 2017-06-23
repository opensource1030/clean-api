<?php

namespace WA\DataStore\Scope;
use WA\DataStore\BaseDataStore;

/**
 * Class Scope.
 */
class Scope extends BaseDataStore
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('WA\DataStore\Permission\Permission', 'scope_permission', 'scope_id', 'permission_id');
    }

    /**
     * Get the Scope by Name.
     *
     * @return Scope
     */
    public static function getByName($name){
        return Scope::where('name', $name)->get();
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new ScopeTransformer();
    }
}