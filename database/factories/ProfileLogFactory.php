<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facebook\Profile;
use App\ProfileLog;
use Faker\Generator as Faker;

$factory->define(ProfileLog::class, function (Faker $faker) {
    return [
        'profile_id'    => factory(Profile::class),
        'duration'      => $faker->numberBetween(100, 5000),
        'miniature'     => $faker->words(3, true),
        'creative'      => $faker->words(10, true),
        'text'          => $faker->text,
        'link'          => $faker->url,
    ];
});
