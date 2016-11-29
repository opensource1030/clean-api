<?php

namespace WA\DataStore\Company;

use Illuminate\Filesystem\Filesystem;
use Log;
use WA\DataStore\BaseDataStore;
use Alert;

/**
 * Class Company.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\User\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Device\Device[] $devices
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Company\Company[] $carriers
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Udl\Udl[] $udls
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\UdlValue\UdlValue[] $udlValues
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Rule\Rule[] $rules
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Allocation\Allocation[] $allocations
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Content\Content[] $contents
 * @property-read \WA\DataStore\User\User $usersCount
 * @property-read mixed $users_count
 * @mixin \Eloquent
 */
class Company extends BaseDataStore
{
    protected $table = 'companies';

    protected $fillable = ['name', 'label', 'active', 'isCensus', 'shortName'];

    protected $morphClass = 'company';

    //@FIXME: should be referenced by it's own ID
    /**
     * @param $companyId
     *
     * @return mixed
     */
    public function getDataMapHeader($companyId)
    {
        $dataMap = $this->dataMaps()
            ->where('dataMapTypeId', 6)
            ->where('carrierId', $companyId)
            ->first(['headers']);

        return $dataMap->headers;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('WA\DataStore\User\User', 'companyId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function devices()
    {
        return $this->belongsToMany('WA\DataStore\Device\Device', 'companies_devices', 'companyId', 'deviceId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function carriers()
    {
        return $this->belongsToMany('WA\DataStore\Company\Company', 'companies_carriers', 'companyId', 'carrierId',
            'billingAccountNumber');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function udls()
    {
        return $this->hasMany('WA\DataStore\Udl\Udl', 'companyId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules()
    {
        return $this->belongsToMany('WA\DataStore\Rule\Rule', 'company_rules', 'companyId', 'ruleId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allocations()
    {
        return $this->hasMany('WA\DataStore\Allocation\Allocation', 'companyId');
    }

    /**
     * Get all contents that belong to a company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contents()
    {
        $contents = $this->morphMany('WA\DataStore\Content\Content', 'owner');

        return $contents;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pools()
    {
        return $this->belongsToMany('WA\DataStore\PoolGroup', 'pool_bases', 'companyId', 'poolGroupId', 'baseCost');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new CompanyTransformer();
    }

    /**
     * Get the row of companyId.
     *
     * @return row
     */
    public function saml2Settings()
    {
        return $this->hasOne('WA\DataStore\Company\CompanySaml2', 'companyId')->first();
    }

    /**
     * Creates the directory if it does not already  exists and return the newly created  path.
     *
     * @param            $determinant | names to split by
     * @param Filesystem $file
     *
     * @return mixed|string
     *
     * @throws \Exception
     **/
    public function makeDirectory(array $determinant, Filesystem $file = null)
    {
        $file = $file ?: new Filesystem();

        if (!$this->active) {
            $this->name = 'zz_Prospect'; // Look in folder for prospect clients
            $newDirectoryPath = $this->rawDataDirectoryPath.DIRECTORY_SEPARATOR.
                $this->name.DIRECTORY_SEPARATOR.
                'Data'.DIRECTORY_SEPARATOR;
        } else {
            $newDirectoryPath = $this->rawDataDirectoryPath.DIRECTORY_SEPARATOR.
                preg_replace('/[^A-Za-z0-9+]/', '', studly_case($this->name)).DIRECTORY_SEPARATOR.
                'Data'.DIRECTORY_SEPARATOR;
        }

        foreach ($determinant as $d) {
            if (strpos($d, '/')) {
                $d = $this->formatNumber($d);
            } else {
                $d = studly_case($d);
            }

            $newDirectoryPath .= $d.DIRECTORY_SEPARATOR;
        }

        if ($file->isDirectory($newDirectoryPath)) {
            return $newDirectoryPath;
        }

        try {
            $file->makeDirectory($newDirectoryPath, 0777, true);

            return $newDirectoryPath;
        } catch (\Exception $e) {
            Log::error('The File could not be created'.$e->getMessage());
            throw new \Exception('Cannot create the directory '.$newDirectoryPath);
        }
    }

    /**
     * Recursively list all the files in a directory.
     *
     * @param            $directoryPath
     * @param Filesystem $file
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function listDirectoryFiles($directoryPath, Filesystem $file = null)
    {
        $file = $file ?: new Filesystem();

        if (!$file->isDirectory($directoryPath)) {
            Log::error('The file path: '.$directoryPath.'does not exist');
            Alert::error(
                'Data directory does not exist.  Please check that it matches the correct schema of [Compnay]\Data\[Carrier]\Data\[YYYY_MM]'
            );

            return false;
        }

        $files = $file->allFiles($directoryPath);

        $fileList = array_filter(
            $files,
            function ($arr) {
                if (in_array(strtolower($arr->getFilename()), ['header-custom.txt'])) {
                    return false;
                } elseif (in_array(strtolower($arr->getExtension()), ['', 'txt', 'csv', 'tab', 'xml'])) {
                    return true;
                } else {
                    return false;
                }
            }
        );

        return $fileList;
    }

    /**
     * @param $number
     *
     * @return string
     */
    private function formatNumber($number)
    {
        // Quickly format a number...
        $stamp = new \DateTime($number);

        return $stamp->format('Y_m');
    }

    public function employeesCount()
    {
        $q = $this->hasOne('WA\DataStore\User\User', 'companyId')
            ->selectRaw('companyId, count(*) as total')
            ->groupBy('companyId');

        return $q;
    }

    public function getUsersCountAttribute()
    {
        if (!array_key_exists('employeesCount', $this->relations)) {
            $this->load('employeesCount');
        }

        $related = $this->getRelation('employeesCount');

        return ($related) ? (int) $related->total : 0;
    }
}
