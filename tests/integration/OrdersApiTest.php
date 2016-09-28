<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class OrdersApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for services
     *
     * @AD: Some changes and a little modification in OrderController@index - Comment ApplyMeta()
     */
    public function testGetOrders()
    {
        factory(\WA\DataStore\Order\Order::class, 40)->create();

        $res = $this->json('GET', 'orders');

        $res->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'status',
                            'userId',
                            'packageId',
                            'deviceId',
                            'serviceId',
                            'created_at',
                            'updated_at',
                        ],
                        'links'
                    ]
                ]
            ]);
    }

    public function testGetOrderById()
    {
        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $res = $this->json('GET', 'orders/'.$order->id)
            ->seeJson([
                'type' => 'orders',
                'status' => $order->status,
                'userId'=> $order->userId,
                'packageId'=> $order->packageId,
                'deviceId'=> $order->deviceId,
                'serviceId'=> $order->serviceId,
            ]);
    }

    public function testGetOrderByIdIfNoExists() {

        $orderId = factory(\WA\DataStore\Order\Order::class)->create()->id;
        $orderId = $orderId + 10;

        $response = $this->call('GET', '/devices/'.$orderId);
        $this->assertEquals(404, $response->status());
    }

    public function testCreateOrder()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();
        $package = factory(\WA\DataStore\Package\Package::class)->create();
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $this->post('/orders',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'OrderStatus',
                        'userId'=> $user->id,
                        'packageId'=> $package->id,
                        'deviceId' => $device->id,
                        'serviceId' => $service->id,
                    ]
                ]
            ])
            ->seeJson([
                'type' => 'orders',
                'status' => 'OrderStatus',
                'userId'=> $user->id,
                'packageId'=> $package->id,
                'deviceId' => $device->id,
                'serviceId' => $service->id,
            ]);
    }

    public function testUpdateOrder()
    {
        $order1 = factory(\WA\DataStore\Order\Order::class)->create();
        $order2 = factory(\WA\DataStore\Order\Order::class)->create();

        $this->assertNotEquals($order1->id, $order2->id);
        $this->assertNotEquals($order1->status, $order2->status);
        $this->assertNotEquals($order1->userId, $order2->userId);
        $this->assertNotEquals($order1->packageId, $order2->packageId);
        $this->assertNotEquals($order1->deviceId, $order2->deviceId);
        $this->assertNotEquals($order1->serviceId, $order2->serviceId);

        $this->put('/orders/'.$order1->id, 
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => $order2->status,
                        'userId'=> $order2->userId,
                        'packageId'=> $order2->packageId,
                        'deviceId' => $order2->deviceId,
                        'serviceId' => $order2->serviceId,
                    ]
                ]
            ])
            ->seeJson([
                'type' => 'orders',
                'id' => "$order1->id",
                'status' => $order2->status,
                'userId'=> $order2->userId,
                'packageId'=> $order2->packageId,
                'deviceId' => $order2->deviceId,
                'serviceId' => $order2->serviceId,
            ]);
    }

    public function testDeleteOrderIfExists() {
        // CREATE & DELETE
        $order = factory(\WA\DataStore\Order\Order::class)->create();
        $responseDel = $this->call('DELETE', '/orders/'.$order->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/orders/'.$order->id);
        $this->assertEquals(404, $responseGet->status());        
    }

    public function testDeleteOrderIfNoExists(){
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/orders/1');
        $this->assertEquals(404, $responseDel->status());
    }
}