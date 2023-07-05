<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\Offer;
use Faker\Generator as Faker;

$factory->define(Offer::class, function (Faker $faker) {
    return [
        'name'  => $faker->words(2, true),
    ];
});
