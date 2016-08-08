<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class UserCarrierTest extends TestCase
{

    use ModelHelpers;

    /**
     * @var WA\DataStore\UserCarrier
     */
    protected $userCarrier;
    private $className = 'WA\DataStore\UserCarrier';

    public function setUp()
    {
        parent::setUp();
        $this->userCarrier = $this->app->make($this->className);
    }

    public function testBelongsToRelationshio()
    {
        // $this->assertBelongsTo('employee', $this->className);
        $this->assertBelongsTo('carrier', $this->className);
    }
}
