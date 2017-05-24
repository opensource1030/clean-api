<?php

class FilteredApiControllerTest extends \TestCase
{
	use \Laravel\Lumen\Testing\DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
    }

    public function testCanConstructAnExtendedController()
    {
        $controller = app()->make('WA\Http\Controllers\DevicesController');
        $this->assertInstanceOf(\WA\Http\Controllers\FilteredApiController::class, $controller);
    }
}
