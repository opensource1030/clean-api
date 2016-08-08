<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;
use Way\Tests\Factory;

class AccountSummaryRepositoryTest extends TestCase
{

    protected $useCleanDatabase = 'sqlite';
    private $accountNumber;
    private $testDump;

    protected $accountSummaryInstance;

    public function setUp()
    {
        parent::setUp();
        $this->generateSeedData();

        $this->accountSummaryInstance = $this->app->make('WA\Repositories\AccountSummaryRepository');
    }

    public function testBoundToRepositoryIntereface()
    {
        $this->assertInstanceOf('WA\Repositories\AccountSummaryRepositoryInterface', $this->accountSummaryInstance);
    }


    public function testGetAccountSummaryMethods()
    {
        $res = $this->accountSummaryInstance->getAccountSummaryByAccountNumber($this->accountNumber);
        $this->assertInstanceOf('WA\DataStore\AccountSummary', $res);

        $resByDump = $this->accountSummaryInstance->getAccountSummaryByAccountNumber(
            $this->accountNumber,
            $this->testDump
        );

        $this->assertEquals($res, $resByDump);

        $resByDumpId = $this->accountSummaryInstance->getAccountSummaryByDumpId($this->testDump->id);
        $this->assertEquals($res, $resByDumpId);
    }

    public function testGetAccountSummaryByDumpAll()
    {
        $resByDumpId = $this->accountSummaryInstance->getAccountSummaryByDumpId($this->testDump->id, true);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $resByDumpId);
    }


    private function generateSeedData()
    {
        $this->testDump = Factory::create('WA\DataStore\Dump');
        $this->accountNumber = rand();
        $accountSummary = Factory::attributesFor(
            'WA\DataStore\AccountSummary',
            [
                'carrierId'               => 2,
                'companyId'               => 1,
                'dumpId'                  => $this->testDump->id,
                'invoiceNumber'           => rand(),
                'preAdjustedTotalCharges' => rand(),
                'billCycleEndDate'        => '2014/04/25',
                'lineCount'               => rand(),
                'billingAccountNumber'    => $this->accountNumber
            ]
        );
        \WA\DataStore\AccountSummary::create($accountSummary);


    }
}
