<?php

class ApiFiltersTest extends TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

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

    // Per JSONAPI, invalid criteria MUST return a 400
    public function testWorkProperlyWithIncorrectSortCriteria()
    {
        $response = $this->call('GET', '/devices?filter[blahblah]=9001');
        $this->assertEquals(400, $response->status());
    }

    public function testCanIncludeFiltersWithOrCriteriaInMeta()
    {
        $this->json('GET', '/devices?include=devicetypes&filter[]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet')
            ->seeJson(['filter' => ['[0]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet']]);
    }

    public function testCanIncludeFiltersWithMultipleOrCriteriaInMeta()
    {
        $this->json('GET', '/devices?include=devicetypes&filter[]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet&filter[]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet')
            ->seeJson(['filter' => ['[0]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet', '[1]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet']]);
    }
}
