<?php

namespace WA\Http\Controllers;

use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use WA\Http\Requests\Parameters\Fields;
use WA\Http\Requests\Parameters\Filters;
use WA\Http\Requests\Parameters\Sorting;
use WA\Helpers\Traits\Criteria;

/**
 * Extensible API controller.
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
        'sort' => [],
        'filters' => [],
        'fields' => [],
    ];

    /**
     * @var status_codes
     */
    public $status_codes = [
        'ok' => 200,
        'created' => 201,       // Object created and return that object.
        'accepted' => 202,
        'createdCI' => 204,
        'badrequest' => 400,    // Bad Request
        'unauthorized' => 401,  // Unauthorized
        'forbidden' => 403,     // Unsupported Request (Permissions).
        'notexists' => 404,     // Get Put or Delete Not Exists Objects.
        'conflict' => 409,       // Other Conflicts.
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
        $filters = new Filters((array) \Request::get('filter', null));

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
    public function isJsonCorrect($request, $type)
    {
        if (!isset($request['data'])) {
            return false;
        } else {
            $data = $request['data'];
            if (!isset($data['type'])) {
                return false;
            } else {
                if ($data['type'] != $type) {
                    return false;
                }
            }
            if (!isset($data['attributes'])) {
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
    public function parseJsonToArray($data, $value)
    {
        $array = array();

        foreach ($data as $info) {
            if (isset($info['type'])) {
                if ($info['type'] == $value) {
                    if (isset($info['id'])) {
                        array_push($array, $info['id']);
                    }
                }
            }
        }

        return $array;
    }

    /*
     *      Gets all the includes and verifies if they are in the includesAvailable variable.
     *
     *      @url: includesAreCorrect.
                        clean.api/devices?include=assets,assets.users,assets.users.assets,assets.users.devices,assets.users.devices.assets,assets.users.devices.carriers,assets.users.devices.companies,assets.users.devices.modifications,assets.users.devices.images,assets.users.devices.prices,assets,assets.users,assets.users.contents,assets.users.allocations,assets.users.roles,assets.devices,assets.devices.assets,assets.devices.carriers,assets.devices.carriers,assets.devices.companies,assets.devices.modifications,assets.devices.images,assets.devices.prices,assets.carriers,assets.carriers.images,assets.companies,carriers,carriers.images,companies,modifications,images,prices,assets,assets.devices,assets.devices.assets,assets.devices.carriers,assets.devices.carriers,assets.devices.companies,assets.devices.modifications,assets.devices.images,assets.devices.prices,assets.carriers,assets.carriers.images,assets.companies,carriers,carriers.images,companies,modifications,images,prices,assets,assets.carriers,assets.carriers.images,assets.companies,carriers,carriers.images,companies,modifications,images,prices
     *
     *      @return: true o false.
     */
    protected function includesAreCorrect($req, $class)
    {

        // Look at if the include parameter exists
        if ($req->has('include')) {
            // Explode the includes.
            $includes = explode(',', $req->input('include'));
        } else {
            return true;
        }

        $exists = true;
        foreach ($includes as $include) {
            $exists = $exists && $this->includesAreCorrectInf($include, $class);

            if (!$exists) {
                break;
            }
        }

        return $exists;
    }

    private function includesAreCorrectInf($include, $class)
    {
        $includesAvailable = $class->getAvailableIncludes();

        $exists = false;
        $includesAux = explode('.', $include);

        if (count($includesAux) == 1) {
            foreach ($includesAvailable as $aic) {
                if ($aic == $includesAux[0]) {
                    $exists = true;
                }
            }

            if (!$exists) {
                return false;
            } else {
                return true;
            }
        } else {
            $includes = substr($include, strlen($includesAux[0]) + 1);

            $var = title_case(str_singular($includesAux[0]));
            $transformer = "\\WA\\DataStore\\$var\\$var".'Transformer';
            $newTransformer = new $transformer();

            return $this->includesAreCorrectInf($includes, $newTransformer);
        }
    }
}
