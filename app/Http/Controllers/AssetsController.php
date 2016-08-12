<?php

namespace WA\Http\Controllers;

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

    public function index()
    {
        $assets = $this->asset->byPage();

        return $this->response()->withPaginator($assets, new AssetTransformer(),['key' => 'assets']);
    }

    /**
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $asset = $this->asset->byId($id);

        return $this->response()->item($asset, new AssetTransformer(),['key' => 'assets']);

    }

}
