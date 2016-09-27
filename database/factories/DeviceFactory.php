<?php
/*
|--------------------------------------------------------------------------
| Devices: Representation of devices we manage
|
| Device Types: types of devices we manage
|--------------------------------------------------------------------------
|
*/


$factory->define(\WA\DataStore\DeviceType\DeviceType::class, 
    function () {

        return []; // Workaround, as this factory is not working for unknown reasons
        /*
        $makes = ['Apple', 'Samsung', 'Blackberry'];
        $models = ['Galaxy S7', 'IPhone SE', 'Q10', 'IPad Air'];
        $class = ['Phone', 'Tablet', 'M2M'];

        return [
            'make' => $makes[array_rand($makes)],
            'model' => $models[array_rand($models)],
            'class' => $class[array_rand($class)],
        ];
        */
    }
);


$factory->define(\WA\DataStore\Device\Device::class, 
    function (\Faker\Generator $faker) {

        $names = ['iPhone 5','iPhone 5S','iPhone 6','iPhone 6S','Samsung G6','Samsung G7','Huawei g620s'];

        return [
            'identification' => $faker->isbn13,
            'name'=> $names[array_rand($names)],
            'properties'=> 'properties',
            //'externalId' => function () {
            //    return factory(WA\DataStore\DeviceType::class)->create()->id;
            //},
            'deviceTypeId' => function () {
                $makes = ['Apple', 'Samsung', 'Blackberry'];
                $models = ['Galaxy S7', 'IPhone SE', 'Q10', 'IPad Air'];
                $class = ['Phone', 'Tablet', 'M2M'];
                $deviceType = factory(\WA\DataStore\DeviceType\DeviceType::class)->make();
                $deviceType->make = $makes[array_rand($makes)];
                $deviceType->model = $models[array_rand($models)];
                $deviceType->class = $class[array_rand($class)];
                $deviceType->save();
                return $deviceType->id;
            }//,
            //'statusId' => function () {
            //    return factory(WA\DataStore\DeviceType::class)->create()->id;
            //},
            //'syncId' => function () {
            //    return factory(WA\DataStore\DeviceType::class)->create()->id;
            //},
        ];
    }
);

//NOTA: function (\Faker\Generator $faker) ???