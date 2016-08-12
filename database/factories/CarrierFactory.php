<?php
/*
|--------------------------------------------------------------------------
| Assets: Representation of companies that we manage
|
| Assets Types: What types of assets are represented
|--------------------------------------------------------------------------
|
*/


$factory->define(\WA\DataStore\Carrier\Carrier::class, function () {

    $carriers = ["ATT", "Verizon", "T-Mobile", "Sprint"];

    return [
        'presentation' => $name = $carriers[array_rand($carriers)],
        'name' => str_replace('-', '_', strtolower($name)),

        'active' => 1,

        'locationId' => function () {
            return factory(WA\DataStore\Location\Location::class)->create()->id;
        },

        'shortName' => $name

    ];
});

