<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class UserAssetTest extends TestCase
{
    use ModelHelpers;

    /**
     * @var WA\DataStore\UserAsset
     */
    protected $userAsset;
    private $className = 'WA\DataStore\UserAsset';

    public function setUp()
    {
        parent::setUp();
        $this->userAsset = $this->app->make($this->className);
    }

    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('device', $this->className);
        $this->assertBelongsTo('asset', $this->className);

    }


}
