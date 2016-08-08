<?php  namespace WA\Services\Queues;

use WA\Testing\TestCase;
use WA\Helpers\Traits\TestMockingHelper;

class ConsolidatorQueueTest extends TestCase {
    use TestMockingHelper;

    protected $consolidatorQueue;

    private $className = 'WA\Services\Queues\ConsolidatorQueue';
    private $carrierDump;
    private $consolidatorManager;
    private $job;

    protected $useCleanDatabase = 'sqlite';

    public function setUp() {
        parent::setUp();


        $this->carrierDump = $this->mock('WA\Repositories\CarrierDumpRepositoryInterface');
        $this->consolidatorManager = $this->mock('WA\Managers\ConsolidatorManagerInterface');

        $this->consolidatorQueue = $this->app->make($this->className);
        $this->job = \Mockery::mock('Illuminate\Queue\Jobs\Job')->shouldReceive('delete')->andReturn(TRUE)->getMock();
        \Log::shouldReceive('debug')->andReturn(TRUE);
        \Log::shouldReceive('error')->andReturn(TRUE);


        \DB::shouldReceive(
            [
                'connection' => \DB::shouldReceive('disableQueryLog')->andReturn(TRUE)->getMock()
            ]
        );


    }

    public function testFiresQueuedJob() {
        $data = ['dump' => 'dataOnGoing', 'scope'=>'all'];
        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Consolidation queued');

        $scarrierDump = $this->getMockCarrierDump('ivd');
        $scarrierDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $this->carrierDump->shouldReceive('find')
            ->andReturn(
                $scarrierDump);

        $this->consolidatorManager->shouldReceive('processCarrierDump')
            ->andReturn(TRUE)
            ->getMock();

        $this->carrierDump->id = 1;

        $this->assertNull($this->consolidatorQueue->fire($this->job, $data));

    }


    /**
     * @expectedException \WA\Exceptions\Consolidator\ConsolidatorQueueException
     * @expectedMessage Invalid dump instance specified.
     */
    public function testThrowErrorIfDumpIsNotInstanceOfClass() {
        $data = ['dump' => 'someData', 'scope' => 'all'];

        $processLogger = $this->mock('WA\Repositories\ProcessLogRepository');
        $processLogger->shouldReceive('create')->once()->andReturn('true');


        $this->carrierDump->shouldReceive('find')
            ->andReturn(
                \Mockery::mock('\WA\DataStore\Dump')
                    ->shouldReceive(
                        [
                            'getAttribute' => 'Funnel queued',
                            'jobstatus'    => \Mockery::self(),
                            'dataMap'      => \Mockery::self()
                        ]
                    )

            );

        $this->consolidatorQueue->fire($this->job, $data);
    }

    /**
     * @expectedException \WA\Exceptions\Consolidator\ConsolidatorQueueException
     * @expectedMessage Dump 1 is not currently awaiting consolidation.
     */
    public function testThrowsExceptionWhenConsolidatorLoaderIsNotInAProperState() {
        $data = ['dump' => 'someData', 'scope' => 'all'];
        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Blah blah');

        $scarrierDump = $this->getMockCarrierDump('ivd');
        $scarrierDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $this->carrierDump->shouldReceive('find')
            ->andReturn(
                $scarrierDump);


        $this->carrierDump->id = 1;

        $this->consolidatorQueue->fire($this->job, $data);

    }


    public function testFiresEventOnFailureASStatusSet() {
        $data = ['dump' => 'someData', 'scope' => 'all'];
        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Consolidation queued');

        $scarrierDump = $this->getMockCarrierDump('ivd');
        $scarrierDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);

        $this->carrierDump->shouldReceive('find')
            ->andReturn($scarrierDump);

        $firedEvent = \Event::shouldReceive('fire')->once()->andReturn(TRUE);

        $this->assertTrue((bool)json_encode($firedEvent));

        $this->consolidatorQueue->fire($this->job, $data);
    }

}
