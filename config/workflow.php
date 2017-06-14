<?php

return [
    'new_order'   => [
        'type'          => 'workflow', // or 'state_machine'
        'marking_store' => [
            'type'      => 'single_state',
            'arguments' => ['currentPlace']
        ],
        'supports'      => ['WA\DataStore\Order\Order'],
        'places'        => ['initial', 'created', 'in_progress', 'rejected', 'fulfilled'],
        'transitions'   => [
            'create' => [
                'from' => 'initial',
                'to'   => 'created'
            ],
            'accept' => [
                'from' => 'created',
                'to'   => 'in_progress'
            ],
            'reject' => [
                'from' => 'created',
                'to'   => 'rejected'
            ],
            'finish' => [
                'from' => 'created',
                'to'   => 'fulfilled'
            ]
        ],
    ]
];