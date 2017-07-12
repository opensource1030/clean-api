<?php

return [

    'default' => 's3',

    'cloud' => 's3',

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
        ],

        's3' => [
	        'driver' => 's3',
	        'key' => env('AWS_IAM_KEY'),
	        'secret' => env('AWS_IAM_SECRET'),
	        'region' => env('AWS_REGION'),
	        'bucket' => env('AWS_BUCKET'),
        ],
    ],

];