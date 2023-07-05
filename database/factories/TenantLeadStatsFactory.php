<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CRM\TenantLeadStats;
use Faker\Generator as Faker;

$factory->define(TenantLeadStats::class, function (Faker $faker) {
    return [
        'assignment_id'    => factory(\App\LeadOrderAssignment::class),
        'exists'           => true,
        'status'           => $faker->word,
        'result'           => $faker->word,
        'closer'           => $faker->name,
        'manager_can_view' => true,
        'last_called_at'   => null,
        'last_viewed_at'   => null,
        'last_updated_at'  => now()->toDateTimeString(),
    ];
});
