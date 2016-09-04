<?php

use Laravel\Lumen\Testing\DatabaseTransactions;


class ApiFiltersTest extends TestCase
{

    use DatabaseTransactions;

    public function testCanIncludeFiltersInMeta()
    {
        $this->json('GET', '/devices?filter[identification]=296')
            ->seeJson(['filter' => ['[identification]=296']]);
    }

    public function testCanIncludeMultipleFiltersInMeta()
    {
        $this->json('GET', '/devices?filter[identification]=296&filter[id]=15')
            ->seeJson(['filter' => ['[identification]=296', '[id]=15']]);
    }

    public function testCanIncludeFiltersWithDelimittedCriteriaInMeta()
    {
        $this->json('GET', '/devices?filter[id]=2,4')
            ->seeJson(['filter' => ['[id]=2,4']]);
    }

    // Make sure we ignore invalid criteria.  Replace with error check if we decide that's better.
    public function testWorkProperlyWithIncorrectSortCriteria()
    {
        $response = $this->call('GET', '/devices?filter[blahblah]=9001');
        $this->assertEquals(200, $response->status());
    }
}
