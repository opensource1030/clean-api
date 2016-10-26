<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PricesApiTest extends TestCase
{
    //use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * A basic functional test for Prices.
     */
    public function testGetPrices()
    {
        factory(\WA\DataStore\Price\Price::class, 40)->create();

        $res = $this->get('prices');

        $res->seeJsonStructure([
            'data' => [
                0 => [
                    'type',
                    'id',
                    'attributes' => [
                        'deviceId',
                        'capacityId',
                        'styleId',
                        'carrierId',
                        'companyId',
                        'priceRetail',
                        'price1',
                        'price2',
                        'priceOwn',
                        'created_at' => [
                            'date',
                            'timezone_type',
                            'timezone',
                        ],
                        'updated_at' => [
                            'date',
                            'timezone_type',
                            'timezone',
                        ],
                    ],
                    'links' => [
                        'self',
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                ],
            ],
            'links' => [
                'self',
                'first',
                'next',
                'last',
            ],
        ]);
    }

    public function testGetPriceById()
    {
        $price = factory(\WA\DataStore\Price\Price::class)->create();

        $res = $this->get('prices/'.$price->id)
            ->seeJson(
            [
                'type' => 'prices',
                'deviceId' => "$price->deviceId",
                'capacityId' => "$price->capacityId",
                'styleId' => "$price->styleId",
                'carrierId' => "$price->carrierId",
                'companyId' => "$price->companyId",
                'priceRetail' => "$price->priceRetail",
                'price1' => "$price->price1",
                'price2' => "$price->price2",
                'priceOwn' => "$price->priceOwn",
            ]);
    }

    public function testCreatePrice()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $capacity = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'capacity']
        );
        $style = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'style']
        );
        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->post('/prices',
            [
                'data' => [
                    'type' => 'prices',
                    'attributes' => [
                        'deviceId' => $device->id,
                        'capacityId' => $capacity->id,
                        'styleId' => $style->id,
                        'carrierId' => $carrier->id,
                        'companyId' => $company->id,
                        'priceRetail' => 300,
                        'price1' => 400,
                        'price2' => 500,
                        'priceOwn' => 600,
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'prices',
                'deviceId' => $device->id,
                'capacityId' => $capacity->id,
                'styleId' => $style->id,
                'carrierId' => $carrier->id,
                'companyId' => $company->id,
                'priceRetail' => 300,
                'price1' => 400,
                'price2' => 500,
                'priceOwn' => 600,
            ]);
    }

    public function testUpdatePrice()
    {
        $device1 = factory(\WA\DataStore\Device\Device::class)->create();
        $capacity1 = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'capacity']
        );
        $style1 = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'style']
        );
        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $company1 = factory(\WA\DataStore\Company\Company::class)->create();

        $device2 = factory(\WA\DataStore\Device\Device::class)->create();
        $capacity2 = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'capacity']
        );
        $style2 = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'style']
        );
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $company2 = factory(\WA\DataStore\Company\Company::class)->create();

        $price = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'deviceId' => $device1->id,
                'capacityId' => $capacity1->id,
                'styleId' => $style1->id,
                'carrierId' => $carrier1->id,
                'companyId' => $company1->id,
                'priceRetail' => 300,
                'price1' => 400,
                'price2' => 500,
                'priceOwn' => 600,
            ]
        );

        $priceAux = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'deviceId' => $device2->id,
                'capacityId' => $capacity2->id,
                'styleId' => $style2->id,
                'carrierId' => $carrier2->id,
                'companyId' => $company2->id,
                'priceRetail' => 350,
                'price1' => 450,
                'price2' => 550,
                'priceOwn' => 650,
            ]
        );

        $this->assertNotEquals($price->id, $priceAux->id);
        $this->assertNotEquals($price->deviceId, $priceAux->deviceId);
        $this->assertNotEquals($price->capacityId, $priceAux->capacityId);
        $this->assertNotEquals($price->styleId, $priceAux->styleId);
        $this->assertNotEquals($price->carrierId, $priceAux->carrierId);
        $this->assertNotEquals($price->companyId, $priceAux->companyId);
        $this->assertNotEquals($price->priceRetail, $priceAux->priceRetail);
        $this->assertNotEquals($price->price1, $priceAux->price1);
        $this->assertNotEquals($price->price2, $priceAux->price2);
        $this->assertNotEquals($price->priceOwn, $priceAux->priceOwn);

        $this->put('/prices/'.$priceAux->id,
            [
                'data' => [
                    'type' => 'prices',
                    'attributes' => [
                        'deviceId' => $price->deviceId,
                        'capacityId' => $price->capacityId,
                        'styleId' => $price->styleId,
                        'carrierId' => $price->carrierId,
                        'companyId' => $price->companyId,
                        'priceRetail' => $price->priceRetail,
                        'price1' => $price->price1,
                        'price2' => $price->price2,
                        'priceOwn' => $price->priceOwn,
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'prices',
                'deviceId' => $price->deviceId,
                'capacityId' => $price->capacityId,
                'styleId' => $price->styleId,
                'carrierId' => $price->carrierId,
                'companyId' => $price->companyId,
                'priceRetail' => $price->priceRetail,
                'price1' => $price->price1,
                'price2' => $price->price2,
                'priceOwn' => $price->priceOwn,
            ]);
    }

    public function testDeletePriceIfExists()
    {
        // CREATE & DELETE
        $price = factory(\WA\DataStore\Price\Price::class)->create();
        $responseDel = $this->call('DELETE', '/prices/'.$price->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/prices/'.$price->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeletepriceIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/prices/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
