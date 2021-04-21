<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\People::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'address' => $faker->address,
        'age' => mt_rand(18, 150),
    ];
});
