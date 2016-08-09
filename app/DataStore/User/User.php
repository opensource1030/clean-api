<?php

namespace WA\DataStore\User;

use Baum\Node;
use Illuminate\Auth\Authenticatable as IllumnateAuthenticableTrait;
use Illuminate\Auth\Passwords\CanResetPassword as IlluminateCanResetPasswordTrait;
use Illuminate\Contracts\Auth\Authenticatable as IllumincateAuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as IlluminateCanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait as RevisionableTrait;
use WA\DataStore\BaseDataStore;
use Zizaco\Entrust\Traits\EntrustUserTrait as EntrustUserTrait;


/**
 * Class User.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\UdlValue\UdlValue[] $udlValues
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Asset\Asset[] $assets
 * @property-read \WA\DataStore\Company\Company $company
 * @property-read \WA\DataStore\Census $census
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Device\Device[] $devices
 * @property-read \WA\DataStore\UdlValuePath\UdlValuePath $department
 * @property-read \WA\DataStore\Location\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Role\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Allocation\Allocation[] $allocations
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Page\Page[] $pages
 * @mixin \Eloquent
 */
class User extends BaseDataStore implements IlluminateCanResetPasswordContract, IllumincateAuthenticatableContract
{
//    use SoftDeletes;
//    use RevisionableTrait;
//    use EntrustUserTrait {
//        EntrustUserTrait::boot insteadof RevisionableTrait;
//    }
//
    use IlluminateCanResetPasswordTrait, IllumnateAuthenticableTrait;

    public $timestamps = true;
    protected $tableName = 'users';
    protected $dontKeepRevisionOf = [
        'lft',
        'rgt',
        'syncId',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'alternateEmail',
        'firstName',
        'alternateFirstName',
        'lastName',
        'companyUserIdentifier',
        'supervisorEmail',
        'isActive',
        'syncId',
        'password',
        'confirmed',
        'confirmed',
        'username',
        'identification',
        'uuid',
        'confirmation_code',
        'notify',
        'notes',
        'defaultLocationId',
        'defaultLang',
        'companyId',
        'departmentId',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $revisionFormattedFields = [
        'isActive' => 'boolean:No|Yes',
        'deleted_at' => 'isEmpty:Active|Deleted',
    ];

    protected $revisionFormattedFieldName = array(
        'firstName' => 'First Name',
        'lastName' => 'Last Name',
        'supervisorEmail' => 'Supervisor Email',
        'deleted_at' => 'Deleted At',
    );

    protected $revisionNullString = 'nothing';
    protected $revisionUnknownString = 'unknown';

    protected $parentColumn = 'supervisorId';
    protected $depthColumn = 'hierarchy';
    protected $leftColumn = 'lft';
    protected $rightColumn = 'rgt';
    protected $guarded = ['supervisorId', 'hierarchy', 'lft', 'rgt'];

    protected $dates = ['deleted_at'];

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return substr($this->email, 0, strpos($this->email, '@'));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function udlValues()
    {
        return $this->belongsToMany('WA\DataStore\UdlValue\UdlValue', 'employee_udls', 'employeeId', 'udlValueId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assets()
    {
        return $this->belongsToMany('WA\DataStore\Asset\Asset', 'employee_assets', 'employeeId', 'assetId');
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
    public function census()
    {
        return $this->belongsTo('WA\DataStore\Census', 'syncId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function devices()
    {
        return $this->belongsToMany('WA\DataStore\Device\Device', 'employee_devices', 'employeeId', 'deviceId');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo('WA\DataStore\UdlValuePath\UdlValuePath', 'departmentId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('WA\DataStore\Location\Location', 'defaultLocationId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('WA\DataStore\Role\Role', 'role_user', 'user_id', 'role_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allocations()
    {
        return $this->hasMany('WA\DataStore\Allocation\Allocation', 'employeeId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pages()
    {
        return $this->belongsToMany('WA\DataStore\Page\Page', 'employees_pages', 'employeeId', 'pageId');
    }


    /**
     * @param $id
     *
     * @return bool
     */
    public function hasDevice($id)
    {
        return !$this->devices->filter(
            function ($device) use ($id) {
                return $device->id == $id;
            }
        )->isEmpty();
    }


}
