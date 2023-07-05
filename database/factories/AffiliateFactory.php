<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(\App\Affiliate::class, function (Faker $faker) {
    return [
        'name'     => $faker->name,
        'offer_id' => factory(\App\Offer::class),
        'api_key'  => \Illuminate\Support\Str::random(16)
    ];
});
