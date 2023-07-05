<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LeadOrderRoute;
use Faker\Generator as Faker;

$factory->define(LeadOrderRoute::class, function (Faker $faker) {
    return [
        'order_id'       => factory(\App\LeadsOrder::class),
        'manager_id'     => 1,
        'offer_id'       => factory(\App\Offer::class),
        'leadsReceived'  => 0,
        'leadsOrdered'   => 0
    ];
});
