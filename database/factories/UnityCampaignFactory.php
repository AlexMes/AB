<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UnityCampaign;
use Faker\Generator as Faker;

$factory->define(UnityCampaign::class, function (Faker $faker) {
    return [
        'id'      => "$faker->uuid",
        'name'    => $faker->word,
        'goal'    => UnityCampaign::GOAL_INSTALLS,
        'enabled' => true,
        'app_id'  => fn () => factory(\App\UnityApp::class)->create(),
    ];
});
