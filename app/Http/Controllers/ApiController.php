<?php

namespace WA\Http\Controllers;

use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use WA\Http\Requests\Parameters\Fields;
use WA\Http\Requests\Parameters\Filters;
use WA\Http\Requests\Parameters\Sorting;

use WA\DataStore\Address\AddressTransformer;
use WA\DataStore\Allocation\AllocationsTransformer;
use WA\DataStore\App\AppTransformer;
use WA\DataStore\Asset\AssetTransformer;
use WA\DataStore\Carrier\CarrierTransformer;
use WA\DataStore\Category\CategoryAppTransformer;
use WA\DataStore\Company\CompanyTransformer;
use WA\DataStore\Condition\ConditionTransformer;
use WA\DataStore\Device\Device;
use WA\DataStore\Device\DeviceTransformer;
use WA\DataStore\DeviceType\DeviceTypeTransformer;
use WA\DataStore\Image\ImageTransformer;
use WA\DataStore\Location\LocationTransformer;
use WA\DataStore\Modification\ModificationTransformer;
use WA\DataStore\Notification\NotificationTransformer;
use WA\DataStore\Order\OrderTransformer;
use WA\DataStore\Package\PackageTransformer;
use WA\DataStore\Preset\PresetTransformer;
use WA\DataStore\Price\PriceTransformer;
use WA\DataStore\Request\RequestTransformer;
use WA\DataStore\Role\RoleTransformer;
use WA\DataStore\Service\ServiceTransformer;

use DB;
use Illuminate\Support\Facades\Lang;
use WA\Helpers\Traits\Criteria;

/**
 * Extensible API controller
 *
 * Class ApiController.
 */
abstract class ApiController extends BaseController
{
    use Helpers;

    /**
     * @var Filters
     */
    protected $filters = null;

    /**
     * @var Sorting
     */
    protected $sort = null;

    /**
     * @var array
     */
    protected $criteria = [
        'sort'    => [],
        'filters' => [],
        'fields'  => []
    ];

    /**
     * @var status_codes
     */
    public $status_codes = [
        'ok' => 200,            //  
        'created' => 201,       // Object created and return that object.
        'accepted' => 202,      //  
        'createdCI' => 204,     //
        'badrequest' => 400,    // Bad Request
        'unauthorized' => 401,  // Unauthorized
        'forbidden' => 403,     // Unsupported Request (Permissions).
        'notexists' => 404,     // Get Put or Delete Not Exists Objects.
        'conflict' => 409       // Other Conflicts.
    ];

    /**
     * @return mixed
     */
    public function getRequestCriteria()
    {
        $filters = $this->getFilters();
        $sort = $this->getSort();
        $fields = $this->getFields();

        $this->criteria['filters'] = $filters;
        $this->criteria['sort'] = $sort;
        $this->criteria['fields'] = $fields;
        return $this->criteria;
    }

    /**
     * @return Sorting
     */
    public function getSort()
    {
        $sort = new Sorting(\Request::get('sort', null));
        return $sort;
    }

    /**
     * @return Filters
     */
    public function getFilters()
    {
        $filters = new Filters((array)\Request::get('filter', null));
        return $filters;
    }


    /**
     * @return Fields
     */
    public function getFields()
    {
        $fields = new Fields(\Request::get('fields', null));
        return $fields;
    }

    public function applyMeta(Response $response)
    {
        $response->addMeta('sort', $this->criteria['sort']->get());
        $response->addMeta('filter', $this->criteria['filters']->get());
        $response->addMeta('fields', $this->criteria['fields']->get());
        return $response;
    }

    public function includeRelationships($modelPlural, $id, $includePlural){

        $model = title_case(str_singular($modelPlural));
        $class = "\\WA\\DataStore\\$model\\$model";

        if(class_exists($class)){
            $results = $class::find($id)->{$includePlural}()->paginate(25);
        } else {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if($results == null){
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response['links']['self'] = '/'.$modelPlural.'/'.$id.'/relationships/'.$includePlural;
        $response['links']['related'] = '/'.$modelPlural.'/'.$id.'/'.$includePlural;

        $resAux = [];
        foreach ( $results as $result ) {
            array_push($resAux, ['type' => $includePlural, 'id' => $result->id]);
        }

        $response['data'] = $resAux;

        return response()->json($response);
    }

    public function includeInformationRelationships($modelPlural, $id, $includePlural)
    {
        $criteria = $this->getRequestCriteria();
        $model = title_case(str_singular($modelPlural));
        $class = "\\WA\\DataStore\\$model\\$model";
        $arrayAttributesModel = \Schema::getColumnListing('prices');

        if(class_exists($class)){
            $results = $class::find($id)->{$includePlural}()->paginate(25);
        } else {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if($results == null){
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response()->withPaginator($results, new PriceTransformer(),['key' => 'prices']);
        $response = $this->applyMeta($response);
        return $response;

/*
        $response['links']['self'] = '/'.$modelPlural.'/'.$id.'/relationships/'.$includePlural;
        $response['links']['related'] = '/'.$modelPlural.'/'.$id.'/'.$includePlural;

        $resAux = [];
        foreach ( $results as $result ) {
            $resultId = $result->id;
            unset($result->id);
            array_push($resAux, ['type' => $includePlural, 'id' => $resultId, 'attributes' => $result]);
        }

        $response['data'] = $resAux;

        return response()->json($response);
*/
    }

    /*
     *      Checks if a JSON param has "data", "type" and "attributes" keys and "type" is equal to $type.
     *
     *      @param: 
     *          "data" : {
     *              "type" : $type,
     *              "attributes" : {
     *              ...
     *      @return:
     *          boolean;
     */
    public function isJsonCorrect($request, $type){

        if(!isset($request['data'])){ 
            return false;
        } else {
            $data = $request['data'];    
            if(!isset($data['type'])){
                return false; 
            } else {
                if($data['type'] <> $type){
                    return false; 
                } 
            }
            if(!isset($data['attributes'])){ 
                return false; 
            }
        }
        return true;
    }

    /*
     *      Transforms an Object to an Array for Sync purposes.
     *
     *      @param:
     *          { "type": "example", "id" : 1 },
     *          { "type": "example", "id" : 2 }
     *      @return
     *          array( 1, 2 );
     */
    public function parseJsonToArray($data, $value){
        $array = array();
        
        foreach ($data as $info) {
            if(isset($info['type'])){
                if($info['type'] == $value){
                    if(isset($info['id'])){
                           array_push($array, $info['id']);    
                    }        
                }
            }                        
        }        
        return $array;
    }

    protected function includesAreCorrect($req, $class){

        if ($req->has('include')) {
            $includes = explode(",", $req->input('include'));
        } else {
            return true;
        }
        
        $avaIncludes = $class->getAvailableIncludes();

        for ($i = 0; $i < count($includes); $i++) {
            $exists = false;
            for ($j = 0; $j < count($avaIncludes); $j++) {
                if($avaIncludes[$j] == $includes[$i]){
                    $exists = true;
                }
            }

            if(!$exists){
                return false;
            }
        }

        return true;
    }

    protected function includesAreCorrectAux($req, $class){

        if ($req->has('include')) {
            $includes = explode(",", $req->input('include'));
        } else {
            return true;
        }

        $avaIncludes = $class->getAvailableIncludes();
        var_dump($avaIncludes);

        for ($i = 0; $i < count($includes); $i++) {
            $exists = false;
            $includesAux = explode(".", $includes[$i]);

            for ($j = 0; $j < count($avaIncludes); $j++) {
                if($avaIncludes[$j] == $includesAux[0]){
                    if(count($includesAux) > 1){
                        var_dump($includesAux[1]);
                        $transformer = $class = "\\WA\\DataStore\\$includesAux[0]\\$includesAux[0]"."Transformer";
                        $avaIncludesAux = $transformer->getAvailableIncludes();

                        var_dump($avaIncludesAux);
                        for($k = 0; count($avaIncludesAux); $k++){
                            var_dump($k);
                            //if($avaIncludesAux[$k] == $includesAux[1]){ $exists = true; }
                        }
                    } else {
                        $exists = true;
                    }
                }
            }

            if(!$exists){
                return false;
            }
        }

        return true;
    }
}


/*        $model = title_case(str_singular($modelPlural));
        $class = "\\WA\\DataStore\\$model\\$model";
        
        $criteria = $this->getRequestCriteria();

        $eloquent = new \WA\Repositories\Device\EloquentDevice(new Device(), app()->make('WA\Repositories\JobStatus\JobStatusInterface'));
        $eloquent->setCriteria($criteria);

        if(class_exists($class)){
            $results = $eloquent->byId($id)->{$includePlural}()->paginate(25);
        } else {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if($results == null){
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response()->withPaginator($results, new PriceTransformer(),['key' => 'prices']);
        $response = $this->applyMeta($response);
        return $response;
*/


/*
public function includeRelationships($modelPlural, $id, $includePlural)
    {
        $model = title_case(str_singular($modelPlural));
        $class = "\\WA\\DataStore\\$model\\$model";

        if(class_exists($class)){
            $results = $class::find($id)->{$includePlural}()->paginate(25);
        } else {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if($results == null){
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response()->withPaginator($results, new PriceTransformer(),['key' => 'prices']);
        $response = $this->applyMeta($response);
        return $response;



        $response['links']['self'] = '/'.$modelPlural.'/'.$id.'/relationships/'.$includePlural;
        $response['links']['related'] = '/'.$modelPlural.'/'.$id.'/'.$includePlural;

        $resAux = [];
        foreach ( $results as $result ) {
            array_push($resAux, ['type' => $includePlural, 'id' => $result->id]);
        }

        $response['data'] = $resAux;

        return response()->json($response);
        $model = title_case(str_singular($modelPlural));
        $class = "\\WA\\DataStore\\$model\\$model";

        if(class_exists($class)){
            $results = $class::find($id)->{$includePlural};
        } else {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if($results == null){
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response['links']['self'] = '/'.$modelPlural.'/'.$id.'/relationships/'.$includePlural;
        $response['links']['related'] = '/'.$modelPlural.'/'.$id.'/'.$includePlural;

        $resAux = [];
        foreach ( $results as $result ) {
            array_push($resAux, ['type' => $includePlural, 'id' => $result->id]);
        }

        $response['data'] = $resAux;

        return response()->json($response);

    }

    public function includeInformationRelationships($modelPlural, $id, $includePlural)
    {
        $this->getRequestCriteria();
        
        $nameModel = title_case(str_singular($modelPlural));
        $model = "\\WA\\DataStore\\$nameModel\\$nameModel";

        //dd($this->criteria['filters']);

        //$arrayAttributesModel = \Schema::getColumnListing('prices');

        if(class_exists($model)){
            $class = $model::find($id)->{$includePlural}()->paginate(25);
        } else {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        //if($results == null){
        //    $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
        //    return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        //}

        //$res = $this->applyCriteria($model, $this->criteria);
        //dd($res);

        $response = $this->response()->withPaginator($class, new PriceTransformer($this->criteria),['key' => 'prices']);
        $response = $this->applyMeta($response);
        return $response;


        $response['links']['self'] = '/'.$modelPlural.'/'.$id.'/relationships/'.$includePlural;
        $response['links']['related'] = '/'.$modelPlural.'/'.$id.'/'.$includePlural;

        $resAux = [];
        foreach ( $results as $result ) {
            $resultId = $result->id;
            unset($result->id);
            array_push($resAux, ['type' => $includePlural, 'id' => $resultId, 'attributes' => $result]);
        }

        $response['data'] = $resAux;

        return response()->json($response);

    }

        //$res = Criteria::filterCriteria($criteria, $class::find($id)->{$includePlural}(), $modelPlural, $arrayAttributesModel);
        //dd($res);
*/