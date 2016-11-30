<?php

$factory->define(WA\DataStore\Address\Address::class, function ($faker) {

	$greekgods = ['Zeus', 'Hera', 'Poseidón', 'Deméter', 'Hestia', 'Hades', 'Ares', 'Hermes', 'Hefesto', 'Atenea', 'Apolo', 'Artemisa', 'Cárites', 'Heracles', 'Dioniso', 'Hebe', 'Perseo', 'Perséfone'];

    return [
    	'name' => $greekgods[array_rand($greekgods)],
        'attn' => $faker->sentence,
        'phone' => $faker->numberBetween(600000000, 699999999),
        'address' => $faker->sentence,
        'city' => $faker->sentence,
        'state' => $faker->sentence,
        'country' => $faker->sentence,
        'postalCode' => $faker->numberBetween(11111, 99999),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
