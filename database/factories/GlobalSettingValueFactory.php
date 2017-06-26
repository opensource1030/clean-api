<?php


$factory->define(WA\DataStore\GlobalSettingValue\GlobalSettingValue::class, function ($faker) {
    $label = ['Enabled', 'Disabled', 'Blocked'];

    return [
        'label' => $var = $label[array_rand($label)],
        'name' => str_replace(" ", "_", strtolower($var)),
        'globalSettingId' => 1,
    ];
});
