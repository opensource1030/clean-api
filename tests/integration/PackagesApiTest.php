<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class PackagesApiTest extends TestCase
{
    use DatabaseTransactions;    
     

    /**
     * A basic functional test for packages
     *
     *
     */
    public function testGetPackages()
    {       
        $res = $this->json('GET', 'packages');

        $res->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'name',
                            'conditionsId',
                            'devicesId',
                            'appsId',
                            'servicesId',
                            'created_at',
                            'updated_at',
                        ],
                        'links'
                    ]
                ]
            ]);
    }

    public function testGetPackageById()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $res = $this->json('GET', 'packages/'.$package->id)
            ->seeJson([
                'type' => 'packages',
                'name' => $package->name,
                'conditionsId'=> $package->conditionsId,
                'devicesId'=> $package->devicesId,
                'appsId'=> $package->appsId,
                'servicesId'=> $package->servicesId,
            ]);
    }

    public function testCreatePackage()
    {
        $condition = factory(\WA\DataStore\Condition\Condition::class)->create();
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $app = factory(\WA\DataStore\App\App::class)->create();
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $this->post('/packages',
            [
                'name' => 'Package Test',
                'conditionsId' => $condition->id,
                'devicesId' => $device->id,
                'appsId' => $app->id,
                'servicesId' => $service->id,
            ])
            ->seeJson([
                'type' => 'packages',
                'name' => 'Package Test',
                'conditionsId' => $condition->id,
                'devicesId' => $device->id,
                'appsId' => $app->id,
                'servicesId' => $service->id,
            ]);
    }

    public function testUpdatePackage()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $this->put('/packages/'.$package->id, [
                'name'=> 'Test Update',
                'conditionsId'=> $package->conditionsId,
                'devicesId'=> $package->devicesId,
                'appsId'=> $package->appsId,
                'servicesId'=> $package->servicesId,
            ])
            ->seeJson([
                'type' => 'packages',
                'name'=> 'Test Update',
                'conditionsId'=> $package->conditionsId,
                'devicesId'=> $package->devicesId,
                'appsId'=> $package->appsId,
                'servicesId'=> $package->servicesId,
            ]);
    }

    public function testDeletePackage()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();
        $this->delete('/packages/'. $package->id);
        $response = $this->call('GET', '/packages/'.$package->id);
        $this->assertEquals(500, $response->status());
    }

}