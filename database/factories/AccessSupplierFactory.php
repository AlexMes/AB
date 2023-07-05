<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AccessSupplier;
use Faker\Generator as Faker;

$factory->define(AccessSupplier::class, function (Faker $faker) {
    return [
        'name'  => $faker->word,
    ];
});
