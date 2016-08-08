<?php

namespace WA\DataStore;

use WA\Testing\TestCase;

class DestinationsTest extends TestCase
{


    public function setUp()
    {
        parent::setUp();
        $this->loadFixture();
    }

    public function testGetCodeList()
    {
        $this->markTestSkipped('Broken fixture');
        $codes = \WA\DataStore\Destinations::getCodeList();

        $this->assertNotEmpty($codes[ 1 ]->iso2);
    }
}
