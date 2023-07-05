<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualBundle;
use Faker\Generator as Faker;

$factory->define(ManualBundle::class, function (Faker $faker) {
    return [
        'name'     => $faker->word,
        'offer_id' => factory(\App\Offer::class),
    ];
});
