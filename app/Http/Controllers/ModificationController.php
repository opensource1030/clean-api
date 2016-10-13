<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Modification\Modification;
use WA\DataStore\Modification\ModificationTransformer;
use WA\Repositories\Modification\ModificationInterface;

use Illuminate\Support\Facades\Lang;

/**
 * Modification resource.
 *
 * @Resource("Modification", uri="/Modification")
 */
class ModificationController extends ApiController
{
    /**
     * @var modificationInterface
     */
    protected $modification;

    /**
     * modification Controller constructor
     *
     * @param modificationInterface $modification
     */
    public function __construct(ModificationInterface $modification)
    {

        $this->modification = $modification;
    }

    /**
     * Show all modification
     *
     * Get a payload of all modification
     *
     */
    public function index()
    {

        $criteria = $this->getRequestCriteria();
        $this->modification->setCriteria($criteria);
        $modification = $this->modification->byPage();

        $response = $this->response()->withPaginator($modification, new ModificationTransformer(),
            ['key' => 'modifications']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single modification
     *
     * Get a payload of a single modification
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->modification->setCriteria($criteria);
        $modification = Modification::find($id);

        if($modification == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Modification']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($modification, new ModificationTransformer(),['key' => 'modifications'])->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Update contents of a modification
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {

        if ($this->isJsonCorrect($request, 'modifications')) {
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $modification = $this->modification->update($data);

                if($modification == 'notExist') {
                    $error['errors']['modification'] = Lang::get('messages.NotExistClass', ['class' => 'Modification']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if($modification == 'notSaved') {
                    $error['errors']['modification'] = Lang::get('messages.NotSavedClass', ['class' => 'Modification']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($modification, new ModificationTransformer(), ['key' => 'modifications'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Modification', 'option' => 'updated', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new modification
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'modifications')) {
            try {
                $data = $request->all()['data']['attributes'];
                $modification = $this->modification->create($data);
                return $this->response()->item($modification, new ModificationTransformer(), ['key' => 'modifications'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Modification', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete a modification
     *
     * @param $id
     */
    public function delete($id)
    {
        $modification = Modification::find($id);
        if ($modification <> null) {
            $this->modification->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Modification']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $modification = Modification::find($id);        
        if($modification == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Modification']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}