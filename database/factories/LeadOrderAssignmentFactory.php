<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LeadOrderAssignment;
use Faker\Generator as Faker;

$factory->define(LeadOrderAssignment::class, function (Faker $faker) {
    return [
        'lead_id'  => factory(\App\Lead::class),
        'route_id' => factory(\App\LeadOrderRoute::class)
    ];
});
