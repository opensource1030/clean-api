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

    $names = ['iPhone 5','iPhone 5S','iPhone 6','iPhone 6S','Samsung G6','Samsung G7','Huawei g620s'];

    $image1 = factory(WA\DataStore\Image\Image::class)->create()->id;
    $image2 = factory(WA\DataStore\Image\Image::class)->create()->id;

    $asset1 = factory(WA\DataStore\Asset\Asset::class)->create()->id;
    $asset2 = factory(WA\DataStore\Asset\Asset::class)->create()->id;
    
    $modification1 = factory(WA\DataStore\Modification\Modification::class)->create()->id;
    $modification2 = factory(WA\DataStore\Modification\Modification::class)->create()->id;
    $modification3 = factory(WA\DataStore\Modification\Modification::class)->create()->id;

    $carrier1 = factory(WA\DataStore\Carrier\Carrier::class)->create()->id;
    $carrier2 = factory(WA\DataStore\Carrier\Carrier::class)->create()->id;

    $company1 = factory(WA\DataStore\Company\Company::class)->create()->id;
    $company2 = factory(WA\DataStore\Company\Company::class)->create()->id;

    $device = factory(WA\DataStore\Device\Device::class)->create()->id;

    $deviceCarrier = factory(WA\DataStore\Device\DeviceCarrier::class)->create();
    $deviceCarrier = factory(WA\DataStore\Device\DeviceCarrier::class)->create();

    


    return [

    ];
});
