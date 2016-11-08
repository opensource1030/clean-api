<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Addon\Addon;
use WA\DataStore\Service\Service;
use WA\DataStore\Service\ServiceTransformer;
use WA\Repositories\Service\ServiceInterface;

use DB;
use Log;

/**
 * Service resource.
 *
 * @Resource("Service", uri="/services")
 */
class ServicesController extends FilteredApiController
{
    /**
     * @var ServiceInterface
     */
    protected $service;

    /**
     * ServicesController constructor.
     *
     * @param ServiceInterface $service
     * @param Request $request
     */
    public function __construct(ServiceInterface $service, Request $request)
    {
        parent::__construct($service, $request);
        $this->service = $service;
    }

    /**
     * Update contents of a Service.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $success = true;
        $dataAddonsFromRequest = $dataAddonsFromDB = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'services')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } else {
            $data = $request->all()['data'];
        }

        DB::beginTransaction();

        /*
         * Now we can update the Service.
         */
        try {
            $data['attributes']['id'] = $id;
            $service = $this->service->update($data['attributes']);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Service', 'option' => 'updated', 'include' => '']);
            $error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        try {
            $addons = Addon::where('serviceId', $id)->get();
            $addonInterface = app()->make('WA\Repositories\Addon\AddonInterface');
        } catch (\Exception $e) {
            $error['errors']['addons'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Service', 'option' => 'updated', 'include' => 'Addons']);
            $error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {
            if (isset($data['relationships']['addons'])) {
                if (isset($data['relationships']['addons']['data'])) {
                    $data = $data['relationships']['addons']['data'];

                    $this->deleteNotRequested($data, $addons, $addonInterface, 'addons');

                    foreach ($data as $addon) {
                        if (isset($addon['id'])) {
                            if ($addon['id'] == 0) {
                                $addonInterface->create($addon);
                            } else {
                                if ($addon['id'] > 0) {
                                    $addonInterface->update($addon);
                                } else {
                                    $success = false;
                                    $error['errors']['addons'] = 'the Addon has an incorrect id';
                                }
                            }
                        } else {
                            $success = false;
                            $error['errors']['addons'] = 'the Addon has no id';
                        }
                    }
                }
            }
        } else {
            foreach ($addons as $addon) {
                $addonInterface->deleteById($addon['id']);
            }
        }
        if ($success) {
            DB::commit();
            return $this->response()->item($service, new ServiceTransformer(), ['key' => 'services'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Create a new Service.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        DB::beginTransaction();

        if ($this->isJsonCorrect($request, 'services')) {
            try {
                $data = $request->all()['data']['attributes'];
                $service = $this->service->create($data);
            } catch (\Exception $e) {
                DB::rollBack();
                $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'Service', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }

            if (isset($data['relationships'])) {
                if (isset($data['relationships']['addons'])) {
                    if (isset($data['relationships']['addons']['data'])) {
                        
                        $addonInterface = app()->make('WA\Repositories\Addon\AddonInterface');
                        $data = $data['relationships']['addons']['data'];

                        foreach ($data as $addon) {
                            try {
                                $addonInterface->create($price);    
                            } catch (\Exception $e) {
                                DB::rollBack();
                                $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Service', 'option' => 'created', 'include' => '']);
                                //$error['errors']['Message'] = $e->getMessage();
                                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                                
                            }
                        }
                    }
                }
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::commit();
        return $this->response()->item($service, new ServiceTransformer(), ['key' => 'services'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a Service.
     *
     * @param $id
     */
    public function delete($id)
    {
        $service = Service::find($id);
        if ($service != null) {
            $this->service->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Service']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $service = Service::find($id);
        if ($service == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Service']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
