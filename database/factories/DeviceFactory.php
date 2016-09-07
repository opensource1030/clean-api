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

    $images = ['link/to/image1.png','link/to/image2.png','link/to/image3.png','link/to/image1.jpg','link/to/image2.jpg'];
    $names = ['iPhone 5','iPhone 5S','iPhone 6','iPhone 6S','Samsung G6','Samsung G7','Huawei g620s'];

    return [
        'identification' => $faker->isbn13,
        'image'=> $images[array_rand($images)],
        'name'=> $names[array_rand($names)],
        'properties'=> 'properties',
        'deviceTypeId' => function () {
            return factory(WA\DataStore\DeviceType::class)->create()->id;
        }
    ];
});

