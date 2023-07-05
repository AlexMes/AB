<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualGroup;
use Faker\Generator as Faker;

$factory->define(ManualGroup::class, function (Faker $faker) {
    return [
        'name'  => $faker->word,
    ];
});
