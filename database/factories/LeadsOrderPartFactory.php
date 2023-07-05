<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LeadOrderRoute;
use Faker\Generator as Faker;

$factory->define(LeadOrderRoute::class, function (Faker $faker) {
    return [
        'order_id'   => factory(\App\LeadsOrder::class),
        'offer_id'   => factory(\App\Offer::class),
        'manager_id' => factory(\App\Manager::class),
        'status'     => LeadOrderRoute::STATUS_ACTIVE,
    ];
});

$factory->state(LeadOrderRoute::class, 'completed', [
    'leadsReceived' => 1,
    'leadsOrdered'  => 1,
]);

$factory->state(LeadOrderRoute::class, 'incomplete', [
    'leadsReceived' => 0,
    'leadsOrdered'  => 10,
]);

$factory->state(LeadOrderRoute::class, 'active', ['status' => LeadOrderRoute::STATUS_ACTIVE]);
$factory->state(LeadOrderRoute::class, 'paused', ['status' => LeadOrderRoute::STATUS_PAUSED]);
