<?php

return [
    'new_order'   => [
        'type'          => 'workflow', // or 'state_machine'
        'marking_store' => [
            'type'      => 'single_state',
            'arguments' => ['status']//, 'userId', 'packageId', 'serviceId', 'addressId', 'orderType', 'serviceImei', 'servicePhoneNo', 'serviceSim', 'deviceImei', 'deviceCarrier', 'deviceSim']
        ],
        'supports'      => ['WA\DataStore\Order\Order'],
        'places'        => ['New', 'Approval', 'Deliver', 'Delivered', 'Denied'],
        'transitions'   => [
            'create' => [
                'from' => 'New',
                'to'   => 'Approval'
            ],
            'accept' => [
                'from' => 'Approval',
                'to'   => 'Deliver'
            ],
            'deny' => [
                'from' => 'Approval',
                'to'   => 'Denied'
            ],
            'send' => [
                'from' => 'Deliver',
                'to'   => 'Delivered'
            ]
        ]
    ]
];