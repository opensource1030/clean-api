<?php

/*
|--------------------------------------------------------------------------
| Locations
|--------------------------------------------------------------------------
|
*/
$factory->define(\WA\DataStore\Location\Location::class, function (\Faker\Generator $faker) {
    return [
        'name' => $faker->country,
        'fullName' => $faker->country,
        'iso2' => $faker->countryISOAlpha3,
        'iso3' => $faker->countryISOAlpha3
    ];
});
