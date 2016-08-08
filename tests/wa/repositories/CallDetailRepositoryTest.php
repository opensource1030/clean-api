<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;
use Way\Tests\Factory;

class CallDetailRepositoryTest extends TestCase {

    protected $repository = NULL;

    protected $useCleanDatabase = 'sqlite';

    protected $testDump;

    public function setUp() {
        parent::setUp();


        $this->repository = \App::make('WA\Repositories\CallDetailRepositoryInterface');
        $this->makeRandomData();
    }


    public function testFinders() {
        $alldata = $this->repository->getAllDataByDump($this->testDump)->get();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $alldata);
        $this->assertEquals(10, $alldata->count());

        $noData = $this->repository->getAllDataByLine('5555555557')->get();
        $this->assertEquals(0, $noData->count());

        $mobileNumber = $this->repository->getAllDataByLine('5555555555')->get();
        $this->assertEquals(6, $mobileNumber->count());

        $mobileWithDump = $this->repository->getAllDataByLineByDump('5555555555', $this->testDump)->get();
        $this->assertEquals(5, $mobileWithDump->count());
    }

    public function testPurgeByDump() {
        $before = $this->repository->getAllDataByDump($this->testDump)->count();
        $deleted = $this->repository->purgeByDumpId($this->testDump->id);
        $after = $this->repository->getAllDataByDump($this->testDump)->count();
        $this->assertEquals($before, $deleted);
        $this->assertEquals(0, $after);
    }

    private function makeRandomData() {
        $this->testDump = Factory::create('WA\DataStore\Dump');

        $bac = rand();
        // Make 5 rows of random data
        for ($i = 0; $i < 5; $i++) {
            $attr = Factory::attributesFor('WA\DataStore\ConsolidatedCdr', [
                    'dumpId' => $this->testDump->id,
                    'mobileNumber' => '6666666666',
                    'carrierId' => 2,
                    'companyId' => 1,
                    'DataType' => 'V',
                    'RoamingIndicator' => '',
                    'BillingAccountNumber' => $bac,
                    'ParentAccountNumber'  => $bac
                ]);
            $this->repository->create($attr);
        }

        // 5 rows of data with a specific mobile number
        for ($i = 0; $i < 5; $i++) {
            $attr = Factory::attributesFor('WA\DataStore\ConsolidatedCdr', [
                    'dumpId' => $this->testDump->id,
                    'mobileNumber' => '5555555555',
                    'carrierId' => 2,
                    'companyId' => 1,
                    'DataType' => 'V',
                    'RoamingIndicator' => '',
                    'BillingAccountNumber' => $bac,
                    'ParentAccountNumber'  => $bac
                ]);
            $this->repository->create($attr);
        }

        // 1 row of data with a specific mobile number, but a different dump id
        $attr = Factory::attributesFor('WA\DataStore\ConsolidatedCdr', [
                'dumpId' => $this->testDump->id + 10,
                'carrierId' => 2,
                'companyId' => 1,
                'DataType' => 'V',
                'RoamingIndicator' => '',
                'BillingAccountNumber' => $bac,
                'ParentAccountNumber'  => $bac,
                'mobileNumber' => '5555555555'
            ]);
        $this->repository->create($attr);

    }

}
