<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class ApiFiltersTest extends TestCase
{
    use DatabaseTransactions;

    public function testCanIncludeFiltersInMeta()
    {
        $this->markTestIncomplete(
          'TODO: needs to be reviewed.'
        );

        $this->json('GET', '/devices?filter[identification]=296')
            ->seeJson(['filter' => ['[identification]=296']]);
    }

    public function testCanIncludeMultipleFiltersInMeta()
    {
        $this->markTestIncomplete(
          'TODO: needs to be reviewed.'
        );

        $this->json('GET', '/devices?filter[identification]=296&filter[id]=15')
            ->seeJson(['filter' => ['[identification]=296', '[id]=15']]);
    }

    public function testCanIncludeFiltersWithDelimittedCriteriaInMeta()
    {
        $this->markTestIncomplete(
          'TODO: needs to be reviewed.'
        );

        $this->json('GET', '/devices?filter[id]=2,4')
            ->seeJson(['filter' => ['[id]=2,4']]);
    }

    // Per JSONAPI, invalid criteria MUST return a 400
    public function testWorkProperlyWithIncorrectSortCriteria()
    {
        $response = $this->call('GET', '/devices?filter[blahblah]=9001');
        $this->assertEquals(400, $response->status());
    }
}
