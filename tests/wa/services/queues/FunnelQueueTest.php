<?php
namespace WA\Services\Queues;

use WA\Testing\TestCase;
use WA\Helpers\Traits\TestMockingHelper;

class FunnelQueueTest extends TestCase {
    use TestMockingHelper;

    protected $funnelQueue;

    private $className = 'WA\Services\Queues\FunnelQueue';
    private $dump;
    private $funnelManager;
    protected $useCleanDatabase = 'sqlite';

    public function setUp() {
        parent::setUp();

        $this->dump = $this->mock('WA\Repositories\DumpRepositoryInterface');
        $this->funnelManager = $this->mock('WA\Managers\FunnelManagerInterface');
        $this->funnelQueue = new $this->className($this->dump, $this->funnelManager);

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
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Funnel queued');
        $mockedDump = $this->mock('WA\DataStore\Dump');
        $mockedDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $mockedDump->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(1);
        $this->dump->shouldReceive('find')
            ->andReturn($mockedDump);

        $this->dump->id = 1;


        $this->funnelManager->shouldReceive('process')->andReturn(TRUE);

        $this->assertNull($this->funnelQueue->fire($this->job, $data));
    }

    /**
     * @expectedException \WA\Exceptions\Funnel\InvalidFunnelQueueException
     * @expectedMessage Invalid dump instance injected into queue for funneling
     */
    public function testThrowErrorIfDumpIsNotInstanceOfClass() {
        $data = ['dump' => 'someData'];

        //$this->dump = \Mockery::mock('\WA\DataStore\CarrierDump');

        $processLogger = $this->mock('WA\Repositories\ProcessLogRepository');
        $processLogger->shouldReceive('create')->once()->andReturn('true');


        $this->dump->shouldReceive('find')
            ->andReturn(
                \Mockery::mock('\WA\DataStore\CarrierDump')
                    ->shouldReceive(
                        [
                            'getAttribute' => 'Funnel queued',
                            'jobstatus'    => \Mockery::self(),
                            'dataMap'      => \Mockery::self()
                        ]
                    )

            );

        $this->funnelQueue->fire($this->job, $data);
    }

    /**
     * @expectedException \WA\Exceptions\Funnel\InvalidFunnelQueueException
     * @expectedMessage Dump 1 is not currently awaiting funneling.
     */
    public function testThrowsExceptionWhenFunnelIsNotInAProperState() {
        $data = ['dump' => 'someData'];

        $processLogger = $this->mock('WA\Repositories\ProcessLogRepository');
        $logged = $processLogger->shouldReceive('create')->once()->andReturn('true');
        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Funnel NOT queued');
        $mockedDump = $this->mock('WA\DataStore\Dump');
        $mockedDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $mockedDump->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(1);
        $this->dump->shouldReceive('find')
            ->andReturn($mockedDump);
        $this->assertTrue((bool)json_encode($logged));

        $this->dump->id = 1;

        $this->funnelQueue->fire($this->job, $data);

    }


    public function testFiresEventOnFailure() {
        $data = ['dump' => 'someData'];

        $mockedJobStatus = $this->mock('WA\DataStore\JobStatus');
        $mockedJobStatus->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('Funnel queued');
        $mockedDump = $this->mock('WA\DataStore\Dump');
        $mockedDump->shouldReceive('getAttribute')->withArgs(['jobstatus'])->andReturn($mockedJobStatus);
        $mockedDump->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(1);
        $this->dump->shouldReceive('find')
            ->andReturn($mockedDump);

        $firedEvent = \Event::shouldReceive('fire')->once()->andReturn(TRUE);

        $this->assertTrue((bool)json_encode($firedEvent));

        $this->funnelQueue->fire($this->job, $data);
    }


}
