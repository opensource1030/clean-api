<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Condition\ConditionOperator;
use WA\DataStore\Condition\ConditionOperatorTransformer;
use WA\Repositories\Condition\ConditionOperatorInterface;
use Illuminate\Support\Facades\Lang;

/**
 * ConditionOperator resource.
 *
 * @Resource("conditionOperator", uri="/condition/Operatorss")
 */
class ConditionOperatorsController extends ApiController
{
    /**
     * @var ConditionOperatorInterface
     */
    protected $conditionOperator;

    /**
     * ConditionOperator Controller constructor.
     *
     * @param ConditionOperatorInterface $conditionOperator
     */
    public function __construct(ConditionOperatorInterface $conditionOperator)
    {
        $this->conditionOperator = $conditionOperator;
    }

    /**
     * Show all ConditionOperator.
     *
     * Get a payload of all ConditionOperator
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->conditionOperator->setCriteria($criteria);
        $conditionOperators = $this->conditionOperator->byPage();

        $response = $this->response()->withPaginator($conditionOperators, new ConditionOperatorTransformer(), ['key' => 'conditionoperators']);
        $response = $this->applyMeta($response);

        return $response;
    }

    /**
     * Show a single ConditionOperator.
     *
     * Get a payload of a single ConditionOperator
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->conditionOperator->setCriteria($criteria);
        $conditionOperator = ConditionOperator::find($id);

        if ($conditionOperator == null) {
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'ConditionOperator']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($conditionOperator, new ConditionOperatorTransformer(), ['key' => 'conditionoperators'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a ConditionOperator.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        if ($this->isJsonCorrect($request, 'conditionOperators')) {
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $conditionOperator = $this->conditionOperator->update($data);

                if ($conditionOperator == 'notExist') {
                    $error['errors']['conditionOperator'] = Lang::get('messages.NotExistClass', ['class' => 'ConditionOperator']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if ($conditionOperator == 'notSaved') {
                    $error['errors']['conditionOperator'] = Lang::get('messages.NotSavedClass', ['class' => 'ConditionOperator']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($conditionOperator, new ConditionOperatorTransformer(), ['key' => 'conditionoperators'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['conditionOperators'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'ConditionOperator', 'option' => 'updated', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new ConditionOperator.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'conditionOperators')) {
            try {
                $data = $request->all()['data']['attributes'];
                $conditionOperator = $this->conditionOperator->create($data);

                return $this->response()->item($conditionOperator, new ConditionOperatorTransformer(), ['key' => 'conditionoperators'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['conditionOperators'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'ConditionOperator', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete an ConditionOperator.
     *
     * @param $id
     */
    public function delete($id)
    {
        $conditionOperator = ConditionOperator::find($id);
        if ($conditionOperator != null) {
            $this->conditionOperator->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'ConditionOperator']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $conditionOperator = ConditionOperator::find($id);
        if ($conditionOperator == null) {
            return array('success' => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'ConditionOperator']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
