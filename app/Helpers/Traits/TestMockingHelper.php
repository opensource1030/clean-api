<?php

namespace WA\Helpers\Traits;

use Mockery as m;

/**
 * Class TestMockingHelper.
 */
trait TestMockingHelper
{
    /**
     * @param $dumpType
     *
     * @return m\MockInterface
     */
    public function getMockCarrierDump($dumpType)
    {
        $mockedDump = $this->getMockDump();

        $mockedCarrier = $this->getMockCarrier();

        $mockedDataMapType = m::mock('WA\DataStore\DataMapType');
        $mockedDataMapType->shouldReceive('getAttribute')->withArgs(['description'])->andReturn('blah');

        $mockedDataMap = m::mock('WA\DataStore\DataMap');
        $mockedDataMap->shouldReceive('getVersionId')->andReturn(true);
        $mockedDataMap->shouldReceive('getAttribute')->withArgs(['type'])->andReturn($dumpType);
        $mockedDataMap->shouldReceive('getAttribute')->withArgs(['dataMapType'])->andReturn($mockedDataMapType);
        $mockedDataMap->shouldReceive('getAttribute')->withArgs(['versionId'])->andReturn(rand());

        $carrierDump = m::mock('WA\DataStore\CarrierDump');
        $carrierDump->shouldReceive('setAttribute');
        $carrierDump->shouldReceive('setStatusByName')->andReturn(true);
        $carrierDump->shouldReceive('getAttribute')->withArgs(['dump'])->andReturn($mockedDump);
        $carrierDump->shouldReceive('getAttribute')->withArgs(['dataMap'])->andReturn($mockedDataMap);
        $carrierDump->shouldReceive('getAttribute')->withArgs(['datamap'])->andReturn($mockedDataMap);
        $carrierDump->shouldReceive('getAttribute')->withArgs(['carrier'])->andReturn($mockedCarrier);
        $carrierDump->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('ATT');
        $carrierDump->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(rand());
        $carrierDump->shouldReceive('getAttribute')->withArgs(['filePath'])->andReturn('rand/file');

        return $carrierDump;
    }

    /**
     * @return m\MockInterface
     */
    public function getMockDump()
    {
        $mockedDump = m::mock('WA\DataStore\Dump');
        $mockedDump->shouldReceive('getAttribute')->withArgs(['carrier'])->andReturn($this->getMockCarrier());
        $mockedDump->shouldReceive('getAttribute')->withArgs(['company'])->andReturn($this->getMockCompany());
        $mockedDump->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(rand());
        $mockedDump->shouldReceive('lineSummaries')->andReturn($this->getMockWirelessLineSummary());
        $mockedDump->shouldReceive('accountSummaries')->andReturn($this->getMockAccountSummary());
        $mockedDump->shouldReceive('setStatusByName')->andReturn(true);

        return $mockedDump;
    }

    /**
     * @return m\MockInterface
     */
    public function getMockCarrier()
    {

        $mockedLocation = $this->getMockLocation();

        $mockedCarrier = m::mock('WA\DataStore\Carrier\Carrier');
        $mockedCarrier->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('att');
        $mockedCarrier->shouldReceive('getAttribute')->withArgs(['presentation'])->andReturn('ATT');
        $mockedCarrier->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(2);
        $mockedCarrier->shouldReceive('getAttribute')->withArgs(['location'])->andReturn($mockedLocation);

        return $mockedCarrier;
    }

    public function getMockLocation() {
        $mockedLocation = m::mock('WA\DataStore\Location\Location');
        $mockedLocation->shouldReceive('getAttribute')->withArgs(['currencyIso'])->andReturn('USD');
        return $mockedLocation;
    }
    /**
     * @return m\MockInterface
     */
    public function getMockCompany()
    {
        $mockedCompany = m::mock('WA\DataStore\Company\Company');
        $mockedCompany->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Babcock');
        $mockedCompany->shouldReceive('getAttribute')->withArgs(['label'])->andReturn('Babcock Power');
        $mockedCompany->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(2);

        return $mockedCompany;
    }

    /**
     * @return m\MockInterface
     */
    public function getMockWirelessLineSummary()
    {
        $mockedWLS = m::mock('WA\DataStore\WirelessLineSummary');
        $mockedWLS->shouldReceive('get')->andReturn();
        $mockedWLS->shouldReceive('count')->andReturn(0);

        return $mockedWLS;
    }

    /**
     * @return m\MockInterface
     */
    public function getMockAccountSummary()
    {
        $mockedAccountSummary = m::mock('WA\DataStore\AccountSummary');
        $mockedAccountSummary->shouldReceive('getAttribute')->withArgs(['carrier'])->andReturn($mockedAccountSummary);
        $mockedAccountSummary->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('ATT');
        $mockedAccountSummary->shouldReceive('getAttribute')->withArgs(['presentation'])->andReturn('ATT');
        $mockedAccountSummary->shouldReceive('getAttribute')->withArgs(['billingAccountNumber'])->andReturn(rand());
        $mockedAccountSummary->shouldReceive('get')->andReturn($mockedAccountSummary);

        return $mockedAccountSummary;
    }
}
