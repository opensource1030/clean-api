<?php
namespace WA\Testing\DataStore\Native\Carriers\Verizon;

use WA\Testing\TestCase;


class IvdDataTest extends TestCase
{
    /**
     * @var \WA\DataStore\Native\Carriers\Verizon\IvdData
     */
    protected $ivdData;

    private $className = 'WA\DataStore\Native\Carriers\Verizon\IvdData';
    private $tableName;
    private $schema;
    private $dumpId;
    private $accountNumnber;
    private $mobileNumber;

    public function setUp()
    {
        $this->markTestSkipped('Broken fixtures');

        parent::setUp();
        $this->schema = base_path() . '/tests/_data/fixtures/sqlite/bruker_vzw_raw_ivd.sql';
        $this->loadFixture();
        $this->tableName = 'native_vzw_test_ivd';
        $this->ivdData = new $this->className($this->tableName);
        $this->dumpId = 1;
        $this->accountNumnber = '485026836-00001';
        $this->mobileNumber = '520-343-2542';
        $this->assertFileExists($this->schema);
        $this->loadFixture($this->schema);
    }

    public function testGetsNativeTable()
    {
        $this->assertEquals($this->tableName, $this->ivdData->getNativeTable());
    }

    public function testGetsInvoiceNumber()
    {
        $this->assertNotEmpty($this->ivdData->getInvoiceNumber($this->dumpId));

    }

    public function testGetsInvoiceDate()
    {
        $this->assertNotEmpty($this->ivdData->getInvoiceDate($this->dumpId));
    }

    public function testGetsParentAccount()
    {
        $this->assertNotEmpty($this->ivdData->getParentAccountNumber($this->dumpId));
    }

    public function testGetsBillingAccount()
    {
        $this->assertNotEmpty($this->ivdData->getBillingAccounts($this->dumpId));
    }

    public function testGetsDistinctWirelessLine()
    {
        $this->assertNotEmpty($this->ivdData->getDistinctWirelessLines($this->dumpId));
    }

    public function testGetsLinesByBillingAccount()
    {
        $this->assertNotEmpty($this->ivdData->getLinesByBillingAccount($this->dumpId, $this->accountNumnber));
    }


    public function testGetsAllDataByLine()
    {
        $this->assertNotEmpty($this->ivdData->getAllDataByLine($this->mobileNumber, $this->dumpId));
    }

    public function testGetsMobileNumber()
    {
        $this->ivdData->{'Wireless Number'} = $this->mobileNumber;
        $expected = str_replace('-', "", $this->mobileNumber);

        $this->assertEquals($expected, $this->ivdData->getMobileNumber());
    }

    public function testGetsUsername()
    {

        $expected = $this->ivdData->{'User Name'};

        $this->assertEquals($expected, $this->ivdData->getUserName());
    }


    public function testGetsUnitType()
    {
        $expected = $this->ivdData->{'Item Type'} = 'MB';
        $this->assertEquals($expected, $this->ivdData->getUnitType());
    }


    public function testGetsBillCycleEndDatae()
    {
        $this->ivdData->{'BillCycleEndDate'} = '12-1';
        $this->assertNotEmpty($this->ivdData->getBillingEndDate());
    }

    public function testGetsBillingEndDate()
    {
        $this->ivdData->{'BillCycleEndDate'} = '12-1';


        $this->assertNotEmpty($this->ivdData->getBillingEndDate());
    }

    public function testGetsChargePeriod()
    {
        $this->ivdData->{'Usage Period'} = 'Monthly Char';
        $this->assertEquals(0, $this->ivdData->getChargePeriod());
    }

    public function testGetsCharge()
    {

        $expected = $this->ivdData->{'Cost'} = 4.55;

        $this->assertEquals($expected, $this->ivdData->getCharge());
    }

    public function testGetsBillabledUsed()
    {
        $expected = $this->ivdData->{'Billable'} = 2234;
        $this->assertEquals($expected, $this->ivdData->getBillableUsed());


        $this->ivdData->{'Billable'} = 'NA';
        $this->assertNull($this->ivdData->getBillableUsed());
    }

    public function testGetsLineInvoiceNumber()
    {
        $expected = $this->ivdData->{'Invoice Number'} = '1234567';
        $this->assertEquals($expected, $this->ivdData->getLineInvoiceNumber());
    }

    public function testGetsTotalUsed()
    {
        $expected = $this->ivdData->{'Used'} = 12345;
        $this->assertEquals($expected, $this->ivdData->getTotalUsed());

        $this->ivdData->{'Used'} = 'NA';
        $this->assertNull($this->ivdData->getTotalUsed());
    }


    public function testGetsAllowance()
    {
        $this->ivdData->{'Allowance'} = 'unlimited';
        $this->assertEquals(999999999, $this->ivdData->getAllowance());

        $this->ivdData->{'Allowance'} = 'NA';
        $this->assertNull($this->ivdData->getAllowance());

        $expected = $this->ivdData->{'Allowance'} = 12344;
        $this->assertEquals($expected, $this->ivdData->getAllowance());
    }

    public function testGetsPooled()
    {
        $this->ivdData->{'Share Description'} = 'Shared Plan';
        $this->assertEquals(1, $this->ivdData->getPooled());

        $this->ivdData->{'Share Description'} = 'Nothing Really';
        $this->assertEquals(0, $this->ivdData->getPooled());
    }

    public function testGetsRatePlanId()
    {
        $this->ivdData->{'Item Description'} = 'NATIONWIDE BUS TALK & TEXT 450';

        $this->assertTrue((bool)$this->ivdData->getRatePlanId());


        $this->ivdData->{'Item Description'} = "something Ramdom";
        $this->assertFalse($this->ivdData->getRatePlanId());

    }

    public function testGetsPooledId()
    {
        $this->ivdData->{'Item Description'} = 'NATIONWIDE BUS TALK & TEXT 450';
        $this->assertTrue((bool)$this->ivdData->getPoolGroupId());

        $this->ivdData->{'Item Description'} = 'Another Random thing';
        $this->assertFalse($this->ivdData->getPoolGroupId());
    }

}
