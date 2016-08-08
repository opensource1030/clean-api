<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class CarrierDumpRepositoryTest extends TestCase
{
    protected $useCleanDatabase = 'sqlite';
    protected $repository = null;

    public function setUp()
    {
        parent::setUp();

        $this->markTestSkipped("Unable to run this test in STRICT mode");

        $this->repository = \App::make('WA\Repositories\CarrierDumpRepositoryInterface');
    }


    /**
     * Test creating the four required dumps for Verizon.
     */
    public function testCreateDumps()
    {
        $vzwDumps =
            array(
                1 =>
                    array('dumpId'           => '1',
                          'originalFileName' => 'Account & Wireless Summary_201311.txt',
                          'filePath'         => '/var/www/app/storage/clients/Bruker/Verizon/2013_11/Account & Wireless Summary_201311.txt',
                          'dataMapId'        => '5',
                    ),
                2 =>
                    array('dumpId'           => '1',
                          'originalFileName' => 'AccountSummary_201311.txt',
                          'filePath'         => '/var/www/app/storage/clients/Bruker/Verizon/2013_11/AccountSummary_201311.txt',
                          'dataMapId'        => '6',
                    ),
                3 =>
                    array('dumpId'           => '1',
                          'originalFileName' => 'Acct & Wireless Charges Detail Summary Usage_201311.txt',
                          'filePath'         => '/var/www/app/storage/clients/Bruker/Verizon/2013_11/Acct & Wireless Charges Detail Summary Usage_201311.txt',
                          'dataMapId'        => '3',
                    ),
                4 =>
                    array('dumpId'           => '1',
                          'originalFileName' => 'Wireless Usage Detail_201311.txt',
                          'filePath'         => '/var/www/app/storage/clients/Bruker/Verizon/2013_11/Wireless Usage Detail_201311.txt',
                          'dataMapId'        => '4'
                    )
            );

        $this->repository->create($vzwDumps);
        $createdDumps = $this->repository->findWhere('dumpId', '1');
        $this->assertEquals(sizeof($vzwDumps), $createdDumps->count());
    }

}
