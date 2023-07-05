<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\TechCost;
use Faker\Generator as Faker;

$factory->define(TechCost::class, function (Faker $faker) {
    return [
        'date'    => $faker->date(),
        'user_id' => factory(\App\User::class)->state('gambler'),
        'spend'   => $faker->numberBetween(1, 100),
    ];
});
