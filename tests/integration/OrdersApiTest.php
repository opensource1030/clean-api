<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class OrdersApiTest extends TestCase
{
    use DatabaseTransactions;     
     

    /**
     * A basic functional test for services
     *
     *
     */
    public function testGetOrders()
    {       
        $res = $this->json('GET', 'orders');

        $res->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'status',
                            'userId',
                            'packageId',
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
            ]);
    }

    public function testCreateOrder()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $this->post('/orders',
            [
                'status' => 'OrderStatus',
                'userId'=> $user->id,
                'packageId'=> $package->id,
            ])
            ->seeJson([
                'type' => 'orders',
                'status' => 'OrderStatus',
                'userId'=> $user->id,
                'packageId'=> $package->id,
            ]);
    }

    public function testUpdateOrder()
    {
        $order = factory(\WA\DataStore\Order\Order::class)->create();
        $user = factory(\WA\DataStore\User\User::class)->create();
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $this->put('/orders/'.$order->id, [
                'status' => 'OrderStatusEdit',
                'userId'=> $user->id,
                'packageId'=> $package->id,
            ])
            ->seeJson([
                'type' => 'orders',
                'status' => 'OrderStatusEdit',
                'userId'=> $user->id,
                'packageId'=> $package->id,
            ]);
    }

    public function testDeleteOrder()
    {
        $order = factory(\WA\DataStore\Order\Order::class)->create();
        $this->delete('/orders/'. $order->id);
        $response = $this->call('GET', '/orders/'.$order->id);
        $this->assertEquals(500, $response->status());
    }

}