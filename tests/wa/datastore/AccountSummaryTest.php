<?php
namespace WA\DataStore;

use WA\Testing\TestCase;

class AccountSummaryTest extends TestCase
{

    private $className = 'WA\DataStore\AccountSummary';

    /**
     * @var \WA\DataStore\AccountSummary
     */
    protected $accountSummary;

    public function setUp()
    {
        parent::setUp();

        $this->accountSummary = $this->app->make($this->className);
    }

    public function testHasManyRelationships()
    {
        $lineSummary = $this->accountSummary->lineSummaries();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $lineSummary);
    }

}

