<?php

namespace WA\Http\Controllers;

use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\Helpers\Traits\Criteria;
use WA\Repositories\AbstractRepository;
use WA\Repositories\RepositoryInterface;

/**
 * Extensible API controller
 *
 * Class ApiController.
 */
abstract class FilteredApiController extends ApiController
{
    use Criteria;

    /**
     * @var null|RepositoryInterface
     */
    protected $resource = null;

    /**
     * @var null|string
     */
    protected $modelName = null;

    /**
     * @var null|string
     */
    protected $modelPlural = null;

    /**
     * @var Request|null
     */
    protected $request = null;

    /**
     * @var bool
     */
    protected $returnEmptyResults = false;

    /**
     * FilteredApiController constructor.
     *
     * @param RepositoryInterface|null $resource
     * @param Request $request
     */
    public function __construct(RepositoryInterface $resource = null, Request $request)
    {
        $this->request = $request;
        $this->resource = $resource;

        // The inheriting controller can set a model plural and model name, or we can guess it
        if ($this->modelPlural === null) {
            $uri = $request->path();
            $this->modelPlural = strpos($uri, "/") === false ? substr($uri, 0) : substr($uri, 0, strpos($uri, "/"));
        }

        if ($this->modelName === null) {
            $this->modelName = title_case(str_singular($this->modelPlural));
        }
    }

    /**
     * Show all resource
     *
     * Get a payload of all resources
     *
     * @Get("/")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10),
     *      @Parameter("access_token", required=true, description="Access token for authentication")
     * })
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!$this->addFilterToTheRequest("index", $request)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $criteria = $this->getRequestCriteria();
        $this->resource->setCriteria($criteria);

        if(($this->modelName == 'Company' || $this->modelName == 'Location') && $request->input('indexAll') == 1)
            $resource = $this->resource->byPage(true, 1000);
        else
            $resource = $this->resource->byPage();

        $transformer = $this->resource->getTransformer();

        if (!$this->includesAreCorrect($request, $transformer)) {
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->paginator($resource, $transformer, ['key' => $this->modelPlural]);
        $response = $this->applyMeta($response);

        return $response;
    }

    /**
     * Show a single resource
     *
     * Get a payload of a single resource by it's ID
     *
     * @Get("/{id}")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function show($id, Request $request)
    {
        if(!$this->addFilterToTheRequest("show", $request)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $criteria = $this->getRequestCriteria();
        $this->resource->setCriteria($criteria);
        $resource = $this->resource->byId($id);
        
        if ($resource === null) {
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => $this->modelName]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $transformer = $this->resource->getTransformer();

        if (!$this->includesAreCorrect($request, $transformer)) {
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->item($resource, $transformer, ['key' => $this->modelPlural]);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * @Get('/{modelPlural}/{id}/relationships/{includePlural}')
     *
     * @param $modelPlural
     * @param $id
     * @param $includePlural
     * @return Response
     */
    public function includeRelationships($modelPlural, $id, $includePlural)
    {
        $model = title_case(str_singular($modelPlural));
        $includeModel = $this->includeModelFunction($modelPlural, $includePlural);

        $transformer = "\\WA\\DataStore\\${model}\\${model}Transformer";
        $includeTransformer = "\\WA\\DataStore\\${includeModel}\\${includeModel}Transformer";

        $class = "WA\\Repositories\\${model}\\${model}Interface";
        $repository = app()->make($class);

        if ($repository === null || !($repository instanceOf AbstractRepository)) {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $criteria = $this->getRequestCriteria();
        $repository->setCriteria($criteria);
        $resource = $repository->byId($id);

        if ($resource === null) {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if (!method_exists($resource, $includePlural)) {
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $includePlural]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $results = $this->applyCriteria($resource->{$includePlural}(), $criteria);

        if ($results === null) {
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->paginator($results->paginate(25), new $includeTransformer(),
            ['key' => $includePlural]);

        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     *
     * @Get('/{modelPlural}/{id}/{includePlural}')
     *
     * @param $modelPlural
     * @param $id
     * @param $includePlural
     * @return Response
     */
    public function includeInformationRelationships($modelPlural, $id, $includePlural)
    {
        $plural = str_plural($modelPlural);
        $includeTC = $this->includeModelFunction($modelPlural, $includePlural);
        $transformer = "\\WA\\DataStore\\$includeTC\\$includeTC" . 'Transformer';

        if ($plural == $modelPlural) {
            $model = title_case(str_singular($modelPlural));
        } else {
            // NOT EXISTS MODEL ( SINGULAR INPUT )
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $modelPlural]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        try {
            $class = "WA\\Repositories\\${model}\\${model}Interface";
            $repository = app()->make($class);
            if ($repository !== null && $repository instanceOf AbstractRepository) {
                $criteria = $this->getRequestCriteria();
                $repository->setCriteria($criteria);
                $resource = $repository->byId($id);
            } else {
                $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }
            $results = $this->applyCriteria($resource->{$includePlural}(), $criteria);
        } catch (\Exception $e) {
            // NOT EXISTS INCLUDE ( NOT IN DATASTORE )
            //$error['errors']['Message'] = $e->getMessage();
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $includePlural]);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if ($results === null) {
            // NOT EXISTS INCLUDE ( NO DATA )
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response->paginator($results->paginate(25), new $transformer(),
            ['key' => $includePlural]);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Fluent method to apply filtering/sorting criteria to response metadata
     *
     * @param Response $response
     * @return Response
     */
    public function applyMeta(Response $response)
    {
        $response->addMeta('sort', $this->criteria['sort']->get());
        $response->addMeta('filter', $this->criteria['filters']->get());
        $response->addMeta('fields', $this->criteria['fields']->get());
        return parent::applyMeta($response);
    }

    /**
     * This function add a company.id filter to each request based on the Model.
     *
     *  @return Boolean
     *  Case1: If no Authenticated user and type is not create: Error.
     *  Case2: If user has the "admin" role: All privileges.
     *  Case3: It user has another role: He/She can only get objects with a relationship with his/her own company.
     *
     */
    public function addFilterToTheRequest($type, $data) {
        $user = \Auth::user();
        
//        if (!isset($user)) {
//            return false;
//        }

        if($user) {
            $role = $user->roles;
            $companyId = $user->companyId;

            if($role[0]->name == 'admin') {

                return true;

            } else {
                if ($type == 'create') { //create
//                return $this->resource->checkModelAndRelationships($data, $companyId);
                    return true;
                } else {
                    if($data != null && $data->input('indexAll') == 0) {
                        $filter = $this->resource->addFilterToTheRequest($companyId);
                        $this->setExtraFilters($filter);
                    }

                    return true;
                }
            }
        } else {
            if ($type == 'create')
                return true;

            return false;
        }
    }

    public function addRelationships($data) {
        $user = \Auth::user();

        if($user) {
            $role = $user->roles;
            if($role[0]->name == 'admin') {
                return $data;
            }
        }

        return $this->resource->addRelationships($data);
    }

    /**
     * When the include is a combination of two words we need to title_case both to create the Transformer.
     * We supose that the includePlural argument has the $model as a substring.
     *
     *  @arg1: $model => represents the model.
     *  @arg2: $includePlural => represents the include
     *
     *  Example: devices/2/relationships/devicetypes
     *  $model = devices
     *  $includePlural = devicetypes
     *  @return DeviceType
     *
     */
    private function includeModelFunction($model, $includePlural){
        
        $modelSingular = str_singular($model);        
        $strlen = strlen($modelSingular);
        $var = strpos($includePlural, $modelSingular);

        if ($var !== false) {
            $substring = substr($includePlural, $strlen);
            return title_case($modelSingular) . title_case(str_singular($substring));
        }

        return title_case(str_singular($includePlural));
    }
}
