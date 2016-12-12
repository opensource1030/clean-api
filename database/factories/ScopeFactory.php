<?php

$factory->define(WA\DataStore\Scope\Scope::class, function ($faker) {
    
    $nameArray = ['get'];
    $name = $nameArray[array_rand($nameArray)];
    $displayArray = ['get'];
    $display = $displayArray[array_rand($displayArray)];
    return [
        
        'name' =>$name,
        'display_name' => $display,
        'description' => $faker->paragraph
    ];
});