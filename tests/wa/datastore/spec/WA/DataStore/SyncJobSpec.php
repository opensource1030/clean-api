<?php

namespace WA\Testing\Spec\DataStore;

use PhpSpec\Laravel\EloquentModelBehavior;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\BaseLaravelBehavior;

class SyncJobSpec extends EloquentModelBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('WA\DataStore\SyncJob');
    }

    function it_should_have_job_status()
    {
        $this->jobStatus()->shouldDefineRelationship('belongsTo', 'WA/DataStore/JobStatus');
    }
}
