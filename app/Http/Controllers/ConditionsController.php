<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Condition\Condition;
use WA\DataStore\Condition\ConditionTransformer;
use WA\Repositories\Condition\ConditionInterface;

/**
 * Condition resource.
 *
 * @Resource("condition", uri="/conditions")
 */
class ConditionsController extends ApiController
{
    /**
     * @var ConditionInterface
     */
    protected $condition;

    /**
     * Condition Controller constructor
     *
     * @param ConditionInterface $condition
     */
    public function __construct(ConditionInterface $condition) {
        
        $this->condition = $condition;
    }

    /**
     * Show all Condition
     *
     * Get a payload of all Condition
     *
     */
    public function index() {

        $criteria = $this->getRequestCriteria();
        $this->condition->setCriteria($criteria);
        $conditions = $this->condition->byPage();

        $response = $this->response()->withPaginator($conditions, new ConditionTransformer(), ['key' => 'conditions']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Condition
     *
     * Get a payload of a single Condition
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request) {

        $condition = Condition::find($id);
        if($condition == null){
            $error['errors']['get'] = 'the Condition selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if(!$this->includesAreCorrect($request, new ConditionTransformer())){
            $error['errors']['getIncludes'] = 'One or More Includes selected doesn\'t exists';
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        return $this->response()->item($condition, new ConditionTransformer(),['key' => 'conditions'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a Condition
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request) {

        if($this->isJsonCorrect($request, 'conditions')){
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $condition = $this->condition->update($data);
                return $this->response()->item($condition, new ConditionTransformer(), ['key' => 'conditions'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['conditions'] = 'the Condition has not been updated';
                //$error['errors']['conditionsMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new Condition
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request) {

        if($this->isJsonCorrect($request, 'conditions')){
            try {
                $data = $request->all()['data']['attributes'];
                $condition = $this->condition->create($data);
                return $this->response()->item($condition, new ConditionTransformer(), ['key' => 'conditions'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['conditions'] = 'the Condition has not been created';
                //$error['errors']['conditionsMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete an Condition
     *
     * @param $id
     */
    public function delete($id) {

        $condition = Condition::find($id);
        if($condition <> null){
            $this->condition->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the Condition selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $this->index();
        $condition = Condition::find($id);
        if($condition == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the Condition has not been deleted';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}