<?php


$factory->define(WA\DataStore\GlobalSetting\GlobalSetting::class, function ($faker) {
    $label = ['Bring Your Own Device is allowed', 'Pay for Your Device is allowed'];

    return [
        'label' => $var = $label[array_rand($label)],
        'name' => str_replace(" ", "_", strtolower($var)),
        'description' => $faker->sentence,
    ];
});
