<?php
namespace WA\DataStore;

use WA\Testing\TestCase;

class AssetTypeTest extends TestCase {

    private $assetTypeModel;

    public function setUp()
    {
        parent::setup();
        $this->assetTypeModel = $this->app->make('WA\DataStore\AssetType');
    }

    public function testImplementsInstanceOfBaseModel()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $this->assetTypeModel);
    }
}
