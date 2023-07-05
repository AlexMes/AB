<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'name'  => $faker->words(2, true),
    ];
});
