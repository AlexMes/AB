<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualPour;
use Faker\Generator as Faker;

$factory->define(ManualPour::class, function (Faker $faker) {
    return [
        'date'    => now(),
        'user_id' => factory(\App\User::class),
    ];
});
