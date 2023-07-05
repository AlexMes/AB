<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TelegramChannel;
use Faker\Generator as Faker;

$factory->define(TelegramChannel::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
    ];
});
