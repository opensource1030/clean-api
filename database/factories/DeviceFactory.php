<?php
/*
|--------------------------------------------------------------------------
| Devices: Representation of devices we manage
|
| Device Types: types of devices we manage
|--------------------------------------------------------------------------
|
*/

$factory->define(\WA\DataStore\DeviceType::class, function () {

    $makes = ['Apple', 'Samsung', 'Blackberry'];
    $models = ['Galaxy S7', 'IPhone SE', 'Q10', 'IPad Air'];
    $class = ['Phone', 'Tablet', 'M2M'];

    return [
        'make' => $makes[array_rand($makes)],
        'model' => $models[array_rand($models)],
        'class' => $class[array_rand($class)],
    ];
});

$factory->define(\WA\DataStore\Device\Device::class, function (\Faker\Generator $faker) {

    return [
        'identification' => $faker->isbn13,

        'deviceTypeId' => function () {
            return factory(WA\DataStore\DeviceType::class)->create()->id;
        }
    ];
});

