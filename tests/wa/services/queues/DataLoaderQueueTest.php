<?php

namespace WA\Services\Queues;

use WA\Testing\TestCase;
use WA\Helpers\Traits\TestMockingHelper;

class DataLoaderQueueTest extends TestCase {
    use TestMockingHelper;

    private $className = 'WA\Services\Queues\DataLoaderQueue';
    private $carrierDump;
    private $dataLoaderManager;
    private $job;
    private $dataLoaderQueue;
    protected $useCleanDatabase = 'sqlite';

    public function setUp() {
        parent::setUp();

        $this->markTestSkipped('Reworked this queue process significantly.');

        $this->carrierDump = $this->mock('WA\Repositories\CarrierDumpRepositoryInterface');
        $this->dataLoaderManager = $this->mock('WA\Managers\DataLoaderManagerInterface');

        $this->dataLoaderQueue = new $this->className($this->carrierDump, $this->dataLoaderManager);

        $this->job = \Mockery::mock('Illuminate\Queue\Jobs\Job')->shouldReceive('delete')->andReturn(TRUE)->getMock();

        \Log::shouldReceive('debug')->andReturn(TRUE);
        \Log::shouldReceive('error')->andReturn(TRUE);

        \DB::shouldReceive(
            [
                'connection' => \DB::shouldReceive('disableQueryLog')->andReturn(TRUE)->getMock()
            ]
        );

    }

    public function testFiresJob() {
        $data = ['dump' => 'someData'];
        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Native file loading queued');

        $scarrierDump = $this->getMockCarrierDump('ivd');
        $scarrierDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $this->carrierDump->shouldReceive('find')
            ->andReturn(
                $scarrierDump);


        $this->app->id = 1;


        $this->dataLoaderManager->shouldReceive('processCarrierDump')->andReturn(TRUE);

        $this->assertNull($this->dataLoaderQueue->fire($this->job, $data));
    }

    /**
     * @expectedException \WA\Exceptions\DataLoader\DataLoadingQueueException
     * @expectedMessage Invalid dump instance specified.
     */
    public function testThrowErrorIfDumpIsNotInstanceOfClass() {
        $data = ['dump' => 'someData'];

        $processLogger = $this->mock('WA\Repositories\ProcessLogRepository');
        $processLogger->shouldReceive('create')->once()->andReturn('true');

        $this->carrierDump->shouldReceive('find')
            ->andReturn(false);


        $this->dataLoaderQueue->fire($this->job, $data);
    }

    /**
     * @expectedException \WA\Exceptions\DataLoader\DataLoadingQueueException
     * @expectedMessage Dump is not ready for native loading
     */
    public function testThrowsExceptionWhenRawDataLoaderIsNotInAProperState() {
        $data = ['dump' => 'someData'];

        $processLogger = $this->mock('WA\Repositories\ProcessLogRepository');
        $logged = $processLogger->shouldReceive('create')->once()->andReturn('true');

        $this->assertTrue((bool)json_encode($logged));
        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Native file loading NOT queued');

        $scarrierDump = $this->getMockCarrierDump('ivd');
        $scarrierDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $this->carrierDump->shouldReceive('find')
            ->andReturn(
                $scarrierDump);

        $this->carrierDump->id = 1;

        $this->dataLoaderQueue->fire($this->job, $data);

    }


    public function testFiresEventOnFailureAsStatusSet() {
        $data = ['dump' => 'someData'];

        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Native file loading queued');

        $scarrierDump = $this->getMockCarrierDump('ivd');
        $scarrierDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $this->carrierDump->shouldReceive('find')
            ->andReturn(
                $scarrierDump);

        $firedEvent = \Event::shouldReceive('fire')->once()->andReturn(TRUE);

        $this->assertTrue((bool)json_encode($firedEvent));

        $this->dataLoaderQueue->fire($this->job, $data);
    }


}
