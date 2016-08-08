<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class AssetTest extends TestCase
{
    use ModelHelpers;

    protected $assetSubject;

    private $className = 'WA\DataStore\Asset\Asset';

    public function testBelongsToManyRelationships()
    {
        $this->assertBelongsToMany('devices', $this->className);

        $this->assertbelongsToMany('employees', $this->className);
    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('wirelessLineDetails', $this->className);
    }

}
