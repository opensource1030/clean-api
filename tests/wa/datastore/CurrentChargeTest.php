<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class CurrentChargeTest extends TestCase
{

    use ModelHelpers;

    private $className = "WA\DataStore\CurrentCharge";

    /**
     * @var \WA\DataStore\CurrentCharge
     */
    protected $currentCharge;


    public function setUp()
    {
        parent::setUp();

        $this->currentCharge = $this->app->make($this->className);
    }

    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('carrier', $this->className);

        $this->assertBelongsTo('company', $this->className);

        $this->assertBelongsTo('asset', $this->className);
    }

}
