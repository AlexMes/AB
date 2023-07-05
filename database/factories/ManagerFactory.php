<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Manager;
use Faker\Generator as Faker;

$factory->define(Manager::class, function (Faker $faker) {
    return [
        'name'            => $faker->firstName,
        'spreadsheet_id'  => $faker->word,
        'email'           => $faker->safeEmail,
        'office_id'       => factory(\App\Office::class)
    ];
});
