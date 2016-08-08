<?php

namespace WA\Testing\DataStore\Native\Carriers\ATT;

use WA\Testing\TestCase;

class IvdDataTest extends TestCase
{

    private $className = 'WA\DataStore\Native\Carriers\ATT\IvdData';

    private $tableName;

    private $schama;

    private $dumpId;

    private $accountNumber;

    private $mobileNumber;
    /**
     * @var WA\DataStore\Native\Carriers\ATT\IvdData
     */
    protected $ivdData;


    public function setUp()
    {
        $this->markTestSkipped('Broken fixtures');

        parent::setUp();

        $this->schama = $dumpsSchema = base_path() . '/tests/_data/fixtures/sqlite/babcock_att_raw_ivd.sql';

        $this->loadFixture();
        $this->loadFixture($dumpsSchema);

        $this->tableName = 'native_att_test_ivd';
        $this->ivdData = new $this->className($this->tableName);
        $this->dumpId = 1;
        $this->accountNumber = $this->ivdData->getBillingAccounts(1) ?: '8512848';
        $this->mobileNumber = '5022954516';
        $this->assertFileExists($this->schama);

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


    public function testGetsParentAccountNumber()
    {
        $this->assertNotEmpty($this->ivdData->getParentAccountNumber($this->dumpId));
    }

    public function testGetsBillingAccounts()
    {
        $this->assertNotEmpty($this->ivdData->getBillingAccounts($this->dumpId));
    }

    public function testGetsTotalCurrentChargesbyAccount()
    {

        $this->assertNotEmpty($this->ivdData->getTotalCurrentChargesByAccount($this->accountNumber, $this->dumpId));
    }


    public function testGetsAccountSummary()
    {
        $this->assertNotEmpty($this->ivdData->getAccountSummary($this->accountNumber, $this->dumpId));
    }


    public function testGetsAccountLevelData()
    {
        $this->assertNotEmpty($this->ivdData->getAccountLevelData($this->dumpId));
    }

    public function testGetsTotalCurrentCharges()
    {
        $this->assertNotEmpty($this->ivdData->getTotalCurrentCharges($this->dumpId));
    }

    public function testGetsInvoiceAmount()
    {
        $this->assertNotEmpty($this->ivdData->getInvoiceNumber($this->dumpId));
    }


    public function testGetsDistinctWirelessLines()
    {
        $this->assertNotEmpty($this->ivdData->getDistinctWirelessLines($this->dumpId));
    }

    public function testGetSummaryByLine()
    {

        $this->assertNotEmpty(
            $this->ivdData->getSummaryByLine(
                $this->mobileNumber,
                $this->dumpId
            )
        );
    }


    public function testGetsAllDataByLine()
    {

        $this->assertNotEmpty($this->ivdData->getAllDataByLine($this->mobileNumber, $this->dumpId));
    }

    public function testGetsMobileNumber()
    {
        $this->ivdData->{'Wireless Number'} = $this->mobileNumber;
        $this->assertNotEmpty($this->ivdData->getMobileNumber());
    }

    public function testGetsBillingAccountNumber()
    {
        $this->ivdData->{'Billing Account Number'} = $this->accountNumber;
        $this->assertNotEmpty($this->ivdData->getBillingAccountNumber());
    }

    public function testGetsUserName()
    {
        $this->assertNotEmpty($this->ivdData->getUserName());
    }

    public function  testGetsCharge()
    {
        $this->ivdData->{'Amount'} = 343.34;
        $this->assertNotEmpty($this->ivdData->getCharge());
    }

    public function testGetsUnitType()
    {
        $this->assertNotEmpty($this->ivdData->getUnitType());
    }

    public function testGetsBillableUsed()
    {
        $this->ivdData->{'Billed Minutes'} = 12324;
        $this->assertNotEmpty($this->ivdData->getBillableUsed());
    }


    public function testGetsTotalUsed()
    {
        $this->ivdData->{'Minutes Used'} = 12324;

        $this->assertNotEmpty($this->ivdData->getTotalUsed());
    }

    public function testGetsAllowance()
    {
        $this->ivdData->{'Minutes Included in Plan'} = 1234;
        $this->assertNotEmpty($this->ivdData->getAllowance());
    }

    public function testGetsChargePeriod()
    {
        $this->assertNotNull($this->ivdData->getChargePeriod());
    }

    public function testGetsBillCycleEndDate()
    {
        $date = '2013-11-10';

        $this->ivdData->{'Period End Date'} = $date;
        $this->assertNotEmpty($date, $this->ivdData->getBillCycleEndDate());
    }

    public function testGetsBillCycleStartDate()
    {
        $this->markTestIncomplete('Change to Period manipulation, incomplete.');

        $date = '2013-11-10';
        $this->ivdData->{'Period'} = $date;

        $this->assertNotEmpty($this->ivdData->getBillingStartDate());
    }


    public function testGetsRatePlanId()
    {
        $this->ivdData->{'Section_4'} = 'ODNNBIBPNTN0UNWUM';
        $this->assertTrue((bool)$this->ivdData->getRatePlanId());
    }


    public function testGetsPoolGroupId()
    {
        $this->ivdData->{'Section_4'} = 'NBPNTN6000UNWUMTM';
        $this->assertTrue((bool)$this->ivdData->getPoolGroupId());
    }
}
