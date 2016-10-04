<?php

namespace WA\Http\Controllers;

use WA\DataStore\Asset\Asset;
use WA\DataStore\Asset\AssetTransformer;
use WA\Repositories\Asset\AssetInterface;

/**
 * Class AssetsController.
 */
class AssetsController extends ApiController
{
    /**
     * @var AssetInterface
     */
    protected $assets;

    /**
     * @param AssetInterface $asset
     */
    public function __construct(AssetInterface $asset)
    {
        $this->asset = $asset;
    }

    /**
     * Show all Assets
     *
     * Get a payload of all Assets
     *
     * @Get("/")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10),
     *      @Parameter("access_token", required=true, description="Access token for authentication")
     * })
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->asset->setCriteria($criteria);
        $assets = $this->asset->byPage();

        $response = $this->response()->withPaginator($assets, new AssetTransformer(), ['key' => 'assets']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->asset->setCriteria($criteria);
        $asset = $this->asset->byId($id);

        if($asset == null){
            $error['errors']['get'] = 'the asset selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        return $this->response()->item($asset, new AssetTransformer(),['key' => 'assets']);
    }
}
