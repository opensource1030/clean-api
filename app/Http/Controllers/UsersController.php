<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Response;
use View;

use WA\Helpers\Traits\SetLimits;
use WA\DataStore\User\User;
use WA\DataStore\User\UserTransformer;
use WA\Repositories\User\UserInterface;

use WA\DataStore\Allocation\Allocation;
use WA\DataStore\Content\Content;
use WA\DataStore\Asset\Asset;

use DB;
use Log;
use Cache;

use Illuminate\Support\Facades\Mail;

/**
 * Users resource.
 *
 * @Resource("Users", uri="/users")
 */
class UsersController extends FilteredApiController
{
    use SetLimits;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * UsersController constructor.
     *
     * @param UserInterface $user
     * @param Request $request
     */
    public function __construct(
        UserInterface $user,
        Request $request
    ) {
        parent::__construct($user, $request);
        $this->user = $user;
    }

    public function numberUsers(Request $request)
    {
        $conditions = $request->all()['data']['conditions'];
        $companyId = $request->all()['data']['companyId'];

        // Retrieve all the users that have the same companyId as the company.
        $users = User::where('companyId', $companyId);
        $usersAux = $users->get();

        $users->where(function ($query) use ($conditions, $usersAux) {
            $query = $query->where('id', 0);

            foreach ($usersAux as $user) {
                $info = $this->retrieveInformationofUser($user);
                $ok = true;

                if ($conditions <> null) {
                    foreach ($conditions as $condition) {
                        foreach ($info as $i) {
                            if ($condition['name'] == $i['label'] && $ok) {
                                switch ($condition['condition']) {
                                    case "like":
                                        $ok = $ok && strpos($i['value'], $condition['value']) !== false;
                                        break;
                                    case "gt":
                                        $ok = $ok && ($i['value'] > $condition['value']) ? true : false;
                                        break;
                                    case "lt":
                                        $ok = $ok && ($i['value'] < $condition['value']) ? true : false;
                                        break;
                                    case "gte":
                                        $ok = $ok && ($i['value'] >= $condition['value']) ? true : false;
                                        break;
                                    case "lte":
                                        $ok = $ok && ($i['value'] <= $condition['value']) ? true : false;
                                        break;
                                    case "ne":
                                        $ok = $ok && ($i['value'] <> $condition['value']) ? true : false;
                                        break;
                                    case "eq":
                                        $ok = $ok && ($i['value'] == $condition['value']) ? true : false;
                                        break;
                                    default:
                                        $ok = $ok && true;
                                }
                            }
                        }
                    }
                }

                if ($ok) {
                    $query = $query->orWhere('id', $user->id);
                }
            }
        });

        return array("number" => $users->count());
    }

    private function retrieveInformationofUser(User $user)
    {
        // Retrieve the UDLSValues.
        $udlValues = $user->UdlValues;

        // Retrieve the user information that will be compared.
        $info = array();
        $auxName = ["value" => $user->username, "name" => "name", "label" => "Name"];
        array_push($info, $auxName);
        $auxEmail = ["value" => $user->email, "name" => "email", "label" => "Email"];
        array_push($info, $auxEmail);

        foreach ($udlValues as $uv) {
            $aux = ["value" => $uv->name, "name" => $uv->udl->name, "label" => $uv->udl->label];
            array_push($info, $aux);
        }

        return $info;
    }

    public function getLoggedInUser(Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->user->setCriteria($criteria);
        $user = Auth::user();

        if ($user === null) {
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => $this->modelName]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if (!$this->includesAreCorrect($request, new UserTransformer())) {
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->item($user, new UserTransformer(), ['key' => $this->modelPlural]);
        $response = $this->applyMeta($response);
        return $response;
    }

    public function store($id, Request $request) 
    {
        $success = true;
        $code = 'conflict';
        $dataAssets = $dataDevices = $dataRoles = $dataUdls = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'users')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can update the User.
         */
        try {
            $data = $request->all()['data'];
            $data['attributes']['id'] = $id;
            $user = $this->user->update($data['attributes']);

            if ($user == 'notExist') {
                $success = false;
                $code = 'notexists';
                $error['errors']['user'] = Lang::get('messages.NotExistClass', ['class' => 'User']);
                //$error['errors']['Message'] = $e->getMessage();
            }

            if ($user == 'notSaved') {
                $success = false;
                $error['errors']['user'] = Lang::get('messages.NotSavedClass', ['class' => 'User']);
                //$error['errors']['Message'] = $e->getMessage();
            }

        } catch (\Exception $e) {
            $success = false;
            $error['errors']['users'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'User', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships']) && $success) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['devicevariations']) && $success) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devicevariations']['data'], 'devicevariations');
                    try {
                        $user->devicevariations()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'updated', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['roles']) && $success) {
                if (isset($dataRelationships['roles']['data'])) {
                    $dataRoles = $this->parseJsonToArray($dataRelationships['roles']['data'], 'roles');
                    try {
                        $user->roles()->sync($dataRoles);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['roles'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'updated', 'include' => 'Roles']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['services']) && $success) {
                if (isset($dataRelationships['services']['data'])) {
                    $dataservices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $user->services()->sync($dataservices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'updated', 'include' => 'Services']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['udls']) && $success) {
                if (isset($dataRelationships['udls']['data'])) {
                    $dataUdls = $this->parseJsonToArray($dataRelationships['udls']['data'], 'udls');
                    try {
                        $user->udlValues()->sync($dataUdls);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['udls'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'updated', 'include' => 'Udls']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['address']) && $success) {
                if (isset($dataRelationships['address']['data'])) {
                    $dataAddress = $this->parseJsonToArray($dataRelationships['address']['data'], 'address');
                    try {
                        $user->address()->sync($dataAddress);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['address'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'updated', 'include' => 'Address']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['allocations']) && $success) {
                if (isset($dataRelationships['allocations']['data'])) {
                    $data = $dataRelationships['allocations']['data'];

                    try {
                        $allocations = Allocation::where('userId', $id)->get();
                        $interfaceA = app()->make('WA\Repositories\Allocation\AllocationInterface');
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['allocations'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'updated', 'include' => 'allocations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }

                    if ($success) {
                        try {                           
                            $this->deleteNotRequested($data, $allocations, $interfaceA, 'allocations');

                            foreach ($data as $allocation) {
                                $allocation['userId'] = $user->id;
                                $allocation['companyId'] = $user->companyId;

                                if (isset($allocation['id'])) {
                                    if ($allocation['id'] == 0) {
                                        $interfaceA->create($allocation);
                                    } else {
                                        if ($allocation['id'] > 0) {
                                            $interfaceA->update($allocation);
                                        } else {
                                            $success = false;
                                            $error['errors']['allocations'] = 'the Allocation has an incorrect id';
                                        }
                                    }
                                } else {
                                    $success = false;
                                    $error['errors']['allocations'] = 'the Allocation has no id';
                                }
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['allocations'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'user', 'option' => 'updated', 'include' => 'allocations']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    }
                }
            }

            if (isset($dataRelationships['contents']) && $success) {
                if (isset($dataRelationships['contents']['data'])) {
                    $data = $dataRelationships['contents']['data'];

                    try {
                        $contents = Content::where('owner_id', $id)->get();
                        $interfaceC = app()->make('WA\Repositories\Content\ContentInterface');
                    } catch (\Exception $e) {
                        $success = false;                        
                        $error['errors']['contents'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'updated', 'include' => 'contents']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }

                    if ($success) {
                        try {                           
                            $this->deleteNotRequested($data, $contents, $interfaceC, 'contents');

                            foreach ($data as $allocation) {
                                    
                                $allocation['owner_id'] = $user->id;

                                if (isset($allocation['id'])) {
                                    if ($allocation['id'] == 0) {
                                        $interfaceC->create($allocation);
                                    } else {
                                        if ($allocation['id'] > 0) {
                                            $interfaceC->update($allocation);
                                        } else {
                                            $success = false;
                                            $error['errors']['contents'] = 'the Content has an incorrect id';
                                        }
                                    }
                                } else {
                                    $success = false;
                                    $error['errors']['contents'] = 'the Content has no id';
                                }
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['contents'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'user', 'option' => 'updated', 'include' => 'contents']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    }
                }
            }
/*
            if (isset($dataRelationships['assets']) && $success) {
                if (isset($dataRelationships['assets']['data'])) {
                    $data = $dataRelationships['assets']['data'];

                    try {
                        $assets = Asset::where('userId', $id)->get();
                        $interfaceAss = app()->make('WA\Repositories\Asset\AssetInterface');
                    } catch (\Exception $e) {
                        $success = false;                        
                        $error['errors']['assets'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'updated', 'include' => 'assets']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }

                    if ($success) {
                        try {                           
                            $this->deleteNotRequested($data, $assets, $interfaceAss, 'assets');

                            foreach ($data as $asset) {
                                    
                                $asset['userId'] = $user->id;

                                if (isset($asset['id'])) {
                                    if ($asset['id'] == 0) {
                                        $interfaceAss->create($asset);
                                    } else {
                                        if ($asset['id'] > 0) {
                                            $interfaceAss->update($asset);
                                        } else {
                                            $success = false;
                                            $error['errors']['assets'] = 'the Asset has an incorrect id';
                                        }
                                    }
                                } else {
                                    $success = false;
                                    $error['errors']['assets'] = 'the Asset has no id';
                                }
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['assets'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'user', 'option' => 'updated', 'include' => 'assets']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    }
                }
            }*/
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($user, new UserTransformer(),
                ['key' => 'users'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes[$code]);
        }
    }

    public function create(Request $request) 
    {
        $success = true;
        $dataAssets = $dataDevices = $dataRoles = $dataUdls = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'users')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can create the User.
         */
        try {
            $data = $request->all()['data'];

            if($this->user->byEmail($data['attributes']['email'])['id'] > 0) {
                $error['errors']['User'] = 'The User can not be created, there are other user with the same email.';
                return response()->json($error)->setStatusCode(409);
            }

            $user = $this->user->create($data['attributes']);
            if(!$user){
                $error['errors']['users'] = 'The User has not been created, some data information is wrong, may be the Email.';
                return response()->json($error)->setStatusCode(409);
            }
        } catch (\Exception $e) {
            $success = false;
            $error['errors']['users'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'User', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships']) && $success) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['devicevariations']) && $success) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $dataDeviceVariations = $this->parseJsonToArray($dataRelationships['devicevariations']['data'], 'devicevariations');
                    try {
                        $user->devicevariations()->sync($dataDeviceVariations);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'created', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['roles']) && $success) {
                if (isset($dataRelationships['roles']['data'])) {
                    $dataRoles = $this->parseJsonToArray($dataRelationships['roles']['data'], 'roles');
                    try {
                        $user->roles()->sync($dataRoles);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['roles'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'created', 'include' => 'Roles']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['udls']) && $success) {
                if (isset($dataRelationships['udls']['data'])) {
                    $dataUdls = $this->parseJsonToArray($dataRelationships['udls']['data'], 'udls');
                    try {
                        $user->udlValues()->sync($dataUdls);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['udls'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'created', 'include' => 'Udls']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['address']) && $success) {
                if (isset($dataRelationships['address']['data'])) {
                    $dataAddress = $this->parseJsonToArray($dataRelationships['address']['data'], 'address');
                    try {
                        $user->address()->sync($dataAddress);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['address'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'User', 'option' => 'created', 'include' => 'Address']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['allocations']) && $success) {
                if (isset($dataRelationships['allocations']['data'])) {
                    $data = $dataRelationships['allocations']['data'];

                    try {
                        $interfaceAll = app()->make('WA\Repositories\Allocation\AllocationInterface');
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['allocations'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'created', 'include' => 'Allocations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }

                    if ($success) {
                        try {
                            foreach ($data as $allocation) {
                                $allocation['userId'] = $user->id;
                                $allocation['companyId'] = $user->companyId;
                                $interfaceAll->create($allocation);
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['allocations'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'created', 'include' => 'Allocations']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    }
                }
            }

            if (isset($dataRelationships['contents']) && $success) {
                if (isset($dataRelationships['contents']['data'])) {
                    $data = $dataRelationships['contents']['data'];

                    try {
                        $interfaceC = app()->make('WA\Repositories\Content\ContentInterface');
                    } catch (\Exception $e) {
                        $success = false;                        
                        $error['errors']['contents'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'created', 'include' => 'contents']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }

                    if ($success) {
                        try {                           
                            foreach ($data as $content) {
                                $content['owner_id'] = $user->id;
                                $interfaceC->create($content);
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['contents'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'user', 'option' => 'created', 'include' => 'contents']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    }
                }
            }
/*
            if (isset($dataRelationships['assets']) && $success) {
                if (isset($dataRelationships['assets']['data'])) {
                    $data = $dataRelationships['assets']['data'];

                    try {
                        $interfaceAss = app()->make('WA\Repositories\Asset\AssetInterface');
                    } catch (\Exception $e) {
                        $success = false;                        
                        $error['errors']['assets'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'created', 'include' => 'Assets']);
                        $error['errors']['Message'] = $e->getMessage();
                    }

                    if ($success) {
                        try {                           
                            foreach ($data as $asset) {
                                $asset['userId'] = $user->id;
                                $interfaceAss->create($asset);
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['assets'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'User', 'option' => 'created', 'include' => 'Assets']);
                            $error['errors']['Message'] = $e->getMessage();
                        }
                    }
                }
            }*/
        }

        if ($success) {
            $code = bin2hex(random_bytes(64));
            $url = $this->request['url'];
            $redirectPath = $url.'/acceptUser/'.$user->identification.'/'.$code;

            $data = [
                'identification' => $user->identification,
                'redirectPath' => $redirectPath,
            ];

            $mail = Mail::send('emails.auth.register', $data, function ($m) use ($user) {
                $m->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
                $m->to($user->email)->subject('New User '.$user->username.' !');
            });

            DB::commit();
            Cache::put('user_email_'.$code, $user->identification, 60);
            Cache::put('user_code_'.$user->identification, $code, 60);
            return $this->response()->item($user, new UserTransformer(), ['key' => 'users'])
                ->setStatusCode($this->status_codes['created']);    
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    public function delete($id) {
        $user = User::find($id);
        if ($user <> null) {
            $this->user->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'User']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $user = User::find($id);
        if ($user == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'User']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /*public function getLoggedInUser(Request $request)
    {
        $user = Auth::user();

        if ($user === null) {
            $error['errors']['scopes'] = 'There\'s no user authenticated';
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $transformer = $user->getTransformer();

        if (!$this->includesAreCorrect($request, $transformer)) {
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->item($user, $transformer, ['key' => 'users']);
        return $response;
    }*/
}
