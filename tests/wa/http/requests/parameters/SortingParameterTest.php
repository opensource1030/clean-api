<?php

namespace WA\Testing\Http\Requests\Parameters;

use TestCase;
use WA\Http\Requests\Parameters\Sorting;

class SortingTest extends TestCase
{
    /**
     * @var Sorting
     */
    private $sorting;

    public function setUp()
    {
        $this->sorting = new Sorting();
        $this->sorting->addField('name', 'asc');
        $this->sorting->addField('id', 'desc');
    }

    public function testGetSorting()
    {
        $this->assertEquals(['name' => 'asc', 'id' => 'desc'], $this->sorting->sorting());
    }

    public function testGetFields()
    {
        $this->assertEquals(['name', 'id'], $this->sorting->fields());
    }

    public function testGet()
    {
        $this->assertEquals('name,-id', $this->sorting->get());
    }

    public function testIsEmpty()
    {
        $this->assertFalse($this->sorting->isEmpty());
    }
}
