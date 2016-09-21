<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Dingo\Api\Routing\Helpers;
use Illuminate\Session\SessionManager as Session;
use WA\DataStore\Package\PackageTransformer;
use WA\Helpers\Traits\SetLimits;
use WA\Http\Controllers\Api\Traits\BasicCrud;
use WA\Repositories\Package\PackageInterface;
use Illuminate\Http\Request;

/**
 * Package resource.
 *
 * @Resource("Package", uri="/Package")
 */
class PackageController extends ApiController
{
    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * Package Controller constructor
     *
     * @param PackageInterface $Package
     */
    public function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }

    /**
     * Show all Package
     *
     * Get a payload of all Package
     *
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->package->setCriteria($criteria);

        $package = $this->package->byPage();
        
        $response = $this->response()->withPaginator($package, new PackageTransformer(),['key' => 'package']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Package
     *
     * Get a payload of a single Package
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $package = $this->package->byId($id);
        return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
    }

    /**
     * Update contents of a Package
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $package = $this->package->update($data);
        return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
    }

    /**
     * Create a new Package
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $package = $this->package->create($data);
        return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
    }

    /**
     * Delete a Package
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->package->deleteById($id);
        $this->index();
    }
}
