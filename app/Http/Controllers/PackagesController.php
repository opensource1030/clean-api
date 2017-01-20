<?php

namespace WA\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Condition\Condition;
use WA\DataStore\Package\Package;
use WA\DataStore\Package\PackageTransformer;
use WA\DataStore\User\User;
use WA\Repositories\Package\PackageInterface;

/**
 * Package resource.
 *
 * @Resource("Package", uri="/Package")
 */
class PackagesController extends FilteredApiController
{
    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * PackagesController constructor.
     *
     * @param PackageInterface $package
     * @param Request $request
     */
    public function __construct(PackageInterface $package, Request $request)
    {
        parent::__construct($package, $request);
        $this->package = $package;
    }

    public function userPackages(Request $request)
    {
        $user = $this->retrieveUserInfoFromAuthorizer();
        $info = $this->retrieveInfoFromUser($user);

        // Retrieve all the packages that have the same companyId as the user.
        $packages = Package::where('companyId', $user->companyId);
        $packagesAux = $packages->get();

        $packages->where(function ($query) use ($info, $packagesAux) {
            foreach ($packagesAux as $package) {
                $conditions = $package->conditions;
                $ok = true;

                if ($conditions != null) {
                    foreach ($conditions as $condition) {
                        foreach ($info as $i) {
                            if ($condition->name == $i['label'] && $ok) {
                                switch ($condition->condition) {
                                    case 'like':
                                        $ok = $ok && strpos($i['value'], $condition->value) !== false;
                                        break;
                                    case 'gt':
                                        $ok = $ok && ($i['value'] > $condition->value) ? true : false;
                                        break;
                                    case 'lt':
                                        $ok = $ok && ($i['value'] < $condition->value) ? true : false;
                                        break;
                                    case 'gte':
                                        $ok = $ok && ($i['value'] >= $condition->value) ? true : false;
                                        break;
                                    case 'lte':
                                        $ok = $ok && ($i['value'] <= $condition->value) ? true : false;
                                        break;
                                    case 'ne':
                                        $ok = $ok && ($i['value'] != $condition->value) ? true : false;
                                        break;
                                    case 'eq':
                                        $ok = $ok && ($i['value'] == $condition->value) ? true : false;
                                        break;
                                    default:
                                        $ok = $ok && true;
                                }
                            }
                        }
                    }
                }

                if ($ok) {
                    $query = $query->orWhere('id', $package->id);
                }
            }
        });

        $packageTransformer = new PackageTransformer();

        if (!$this->includesAreCorrect($request, $packageTransformer)) {
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        return $this->response()->withPaginator($packages->paginate(25), $packageTransformer,
            ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
    }

    private function retrieveUserInfoFromAuthorizer()
    {
        // Retrieve the current user.
        $id = Authorizer::getResourceOwnerId();
        return User::find($id);
    }

    private function retrieveInfoFromUser($user)
    {
        $udlValues = $user->UdlValues;

        // Retrieve the user information that will be compared.
        $info = array();
        $auxName = ['value' => $user->username, 'name' => 'name', 'label' => 'Name'];
        array_push($info, $auxName);
        $auxEmail = ['value' => $user->email, 'name' => 'email', 'label' => 'Email'];
        array_push($info, $auxEmail);
        $auxBudget = ['value' => '', 'name' => 'budget', 'label' => 'Budget'];
        array_push($info, $auxBudget);

        foreach ($udlValues as $uv) {
            $aux = ['value' => $uv->name, 'name' => $uv->udl->name, 'label' => $uv->udl->label];
            array_push($info, $aux);
        }

        $auxBudget = ['value' => '', 'name' => 'budget', 'label' => 'Budget'];
        array_push($info, $auxBudget);
        $auxCountry1 = ['value' => '', 'name' => 'country', 'label' => 'Country'];
        array_push($info, $auxCountry1);
        $auxCountry2 = ['value' => '', 'name' => 'country', 'label' => 'Country'];
        array_push($info, $auxCountry2);
        $auxCity = ['value' => '', 'name' => 'city', 'label' => 'City'];
        array_push($info, $auxCity);
        $auxAddress = ['value' => '', 'name' => 'address', 'label' => 'Address'];
        array_push($info, $auxAddress);

        return $info;
    }

    /**
     * Update contents of a Package.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $success = true;
        $dataConditions = $dataServices = $dataDevices = $dataApps = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'packages')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can create the Package.
         */
        try {
            $data = $request->all()['data'];
            $data['attributes']['id'] = $id;
            $package = $this->package->update($data['attributes']);

            if ($package == 'notExist') {
                DB::rollBack();
                $error['errors']['package'] = Lang::get('messages.NotExistClass', ['class' => 'Package']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if ($package == 'notSaved') {
                DB::rollBack();
                $error['errors']['package'] = Lang::get('messages.NotSavedClass', ['class' => 'Package']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['packages'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Package', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['conditions'])) {
                if (isset($dataRelationships['conditions']['data'])) {
                    $dataConditions = $this->parseJsonToArray($dataRelationships['conditions']['data'], 'conditions');
                    try {
                        $package->conditions()->sync($dataConditions);
                    } catch (\Exception $e) {
                        $error['errors']['conditions'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'updated', 'include' => 'Conditions']);
                        //$error['errors']['conditionsMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['services'])) {
                if (isset($dataRelationships['services']['data'])) {
                    $dataServices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $package->services()->sync($dataServices);
                    } catch (\Exception $e) {
                        $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'updated', 'include' => 'Services']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['devicevariations'])) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devicevariations']['data'], 'devicevariations');
                    try {
                        $package->devicevariations()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'updated', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['apps'])) {
                if (isset($dataRelationships['apps']['data'])) {
                    $dataApps = $this->parseJsonToArray($dataRelationships['apps']['data'], 'apps');
                    try {
                        $package->apps()->sync($dataApps);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'updated', 'include' => 'Apps']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($package, new PackageTransformer(),
                ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Create a new Package.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $success = true;
        $dataConditions = $dataServices = $dataDevices = $dataApps = $dataDelivery = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'packages')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can create the Package.
         */
        try {
            $data = $request->all()['data'];
            $package = $this->package->create($data['attributes']);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['packages'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Package', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['conditions'])) {
                if (isset($dataRelationships['conditions']['data'])) {
                    $dataConditions = $this->parseJsonToArray($dataRelationships['conditions']['data'], 'conditions');
                    try {
                        $package->conditions()->sync($dataConditions);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['conditions'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'created', 'include' => 'Conditions']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['services'])) {
                if (isset($dataRelationships['services']['data'])) {
                    $dataServices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $package->services()->sync($dataServices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'created', 'include' => 'Services']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
            
            if (isset($dataRelationships['devicevariations'])) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devicevariations']['data'], 'devicevariations');
                    try {
                        $package->devicevariations()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'updated', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['apps'])) {
                if (isset($dataRelationships['apps']['data'])) {
                    $dataApps = $this->parseJsonToArray($dataRelationships['apps']['data'], 'apps');
                    try {
                        $package->apps()->sync($dataApps);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Package', 'option' => 'created', 'include' => 'Apps']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($package, new PackageTransformer(),
                ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Delete a Package
     *
     * @param $id
     */
    public function delete($id)
    {
        $package = Package::find($id);
        if ($package != null) {
            $this->package->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Package']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $package = Package::find($id);

        if ($package == null) {
            return array('success' => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Package']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
