<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Placement;
use Faker\Generator as Faker;

$factory->define(Placement::class, function (Faker $faker) {
    return [
        'name'  => $faker->sentence(3),
    ];
});
