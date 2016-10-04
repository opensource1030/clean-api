<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Modification\Modification;
use WA\DataStore\Modification\ModificationTransformer;
use WA\Repositories\Modification\ModificationInterface;

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
    public function __construct(ModificationInterface $modification) {

        $this->modification = $modification;
    }

    /**
     * Show all modification
     *
     * Get a payload of all modification
     *
     */
    public function index() {

        $criteria = $this->getRequestCriteria();
        $this->modification->setCriteria($criteria);
        $modification = $this->modification->byPage();
      
        $response = $this->response()->withPaginator($modification, new ModificationTransformer(),['key' => 'modifications']);
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
    public function show($id) {

        $modification = Modification::find($id);
        if($modification == null){
            $error['errors']['get'] = 'the modification selected doesn\'t exists';   
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
    public function store($id, Request $request) {

        if($this->isJsonCorrect($request, 'modifications')){
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $modification = $this->modification->update($data);
                return $this->response()->item($modification, new ModificationTransformer(), ['key' => 'modifications'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['modifications'] = 'the Modification has not been updated';
                //$error['errors']['modificationsMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new modification
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request) {

        if($this->isJsonCorrect($request, 'modifications')){
            try {
                $data = $request->all()['data']['attributes'];
                $modification = $this->modification->create($data);
                return $this->response()->item($modification, new ModificationTransformer(), ['key' => 'modifications'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['modifications'] = 'the Modification has not been created';
                //$error['errors']['modificationsMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
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
        if($modification <> null){
            $this->modification->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the modification selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $this->index();
        $modification = Modification::find($id);        
        if($modification == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the modification has not been deleted';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}