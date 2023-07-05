<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facebook\Profile;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'name'    => $faker->name,
        'fbId'    => $faker->bankAccountNumber,
        'token'   => \Illuminate\Support\Str::random(15),
        'user_id' => factory(\App\User::class)->create()->id,
    ];
});
