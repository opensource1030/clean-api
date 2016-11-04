<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Service\Service;
use WA\DataStore\Service\ServiceTransformer;
use WA\Repositories\Service\ServiceInterface;

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
        if ($this->isJsonCorrect($request, 'services')) {
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $service = $this->service->update($data);

                if ($service == 'notExist') {
                    $error['errors']['service'] = Lang::get('messages.NotExistClass', ['class' => 'Service']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if ($service == 'notSaved') {
                    $error['errors']['service'] = Lang::get('messages.NotSavedClass', ['class' => 'Service']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($service, new ServiceTransformer(),
                    ['key' => 'services'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'Service', 'option' => 'updated', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new Service.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'services')) {
            try {
                $data = $request->all()['data']['attributes'];
                $service = $this->service->create($data);

                return $this->response()->item($service, new ServiceTransformer(),
                    ['key' => 'services'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'Service', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
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
