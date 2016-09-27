<?php


$factory->define(WA\DataStore\Address\Address::class, function ($faker) {

	$address = ['address1','address2','address3','address4','address5','address6','address7'];
    $city = ['city1','city2','city3','city4','city5','city6','city7'];
    $state = ['state1','state2','state3','state4','state5','state6','state7'];
    $country = ['country1','country2','country3','country4','country5','country6','country7'];
    $postalCode = ['postalCode1','postalCode2','postalCode3','postalCode4','postalCode5','postalCode6','postalCode7'];

    return [
        'address' => $address[array_rand($address)],
        'city' => $city[array_rand($city)],
        'state' => $state[array_rand($state)],
        'country' => $country[array_rand($country)],
        'postalCode' => $postalCode[array_rand($postalCode)],
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});