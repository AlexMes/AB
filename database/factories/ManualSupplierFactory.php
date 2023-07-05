<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualSupplier;
use Faker\Generator as Faker;

$factory->define(ManualSupplier::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
