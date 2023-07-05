<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LeadDestination;
use Faker\Generator as Faker;

$factory->define(LeadDestination::class, function (Faker $faker) {
    return [
        'name'      => $faker->word,
        'driver'    => \App\LeadDestinationDriver::DEFAULT,
    ];
});
