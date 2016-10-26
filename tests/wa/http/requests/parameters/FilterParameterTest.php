<?php

namespace WA\Testing\Http\Requests\Parameters;

use TestCase;
use WA\Http\Requests\Parameters\Filters;

class FilterParameterTest extends TestCase
{
    /**
     * @var Filters
     */
    private $filters;

    public function setUp()
    {
        $this->filters = new Filters();
    }

    public function testInputViaConstructor()
    {
        $filters = new Filters(['[name]=123']);
        $this->assertEquals([0 => ['eq' => '[name]=123']], $filters->filtering());
    }
}
