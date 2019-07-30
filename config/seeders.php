<?php

return [

    'mockaroo_url' => env('MOCKAROO_URL', ''),

    'mockaroo_key' => env('MOCKAROO_KEY', ''),

    'mockaroo_codes' => [
        'address' => array( 'code' => '028deab0', 'numitems' => env('MOCKAROO_COUNT_ADDRESS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'allocations' => array( 'code' => 'af419940', 'numitems' => env('MOCKAROO_COUNT_ALLOCATIONS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'carrier_devices' => array( 'code' => 'fb5e6210', 'numitems' => env('MOCKAROO_COUNT_CARRIER_DEVICES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'carrier_images' => array( 'code' => '455cfbb0', 'numitems' => env('MOCKAROO_COUNT_CARRIER_IMAGES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'carriers' => array( 'code' => 'f1487690', 'numitems' => env('MOCKAROO_COUNT_CARRIERS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'companies' => array( 'code' => 'cfe68ba0', 'numitems' => env('MOCKAROO_COUNT_COMPANIES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'devices' => array( 'code' => '4c6ac1a0', 'numitems' => env('MOCKAROO_COUNT_DEVICES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'orders' => array( 'code' => '49a6d200', 'numitems' => env('MOCKAROO_COUNT_ORDERS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'package_address' => array( 'code' => '9a169080', 'numitems' => env('MOCKAROO_COUNT_PACKAGE_ADDRESS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'package_apps' => array( 'code' => 'ceb40b70', 'numitems' => env('MOCKAROO_COUNT_PACKAGE_APPS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'package_devices' => array( 'code' => '028a4210', 'numitems' => env('MOCKAROO_COUNT_PACKAGE_DEVICES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'package_services' => array( 'code' => '340a4fa0', 'numitems' => env('MOCKAROO_COUNT_PACKAGE_SERVICES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'package_settings' => array( 'code' => '58903b00', 'numitems' => env('MOCKAROO_COUNT_PACKAGE_SETTINGS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'packages' => array( 'code' => '954bc3a0', 'numitems' => env('MOCKAROO_COUNT_PACKAGES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'services' => array( 'code' => '4b9307c0', 'numitems' => env('MOCKAROO_COUNT_SERVICES', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
        'users' => array( 'code' => 'a35474b0', 'numitems' => env('MOCKAROO_COUNT_USERS', 1000), 'itemsPerPage' => env('MOCKAROO_ITEMS_SPLIT', 300) ),
    ],

];
