<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UnityInsight;
use Faker\Generator as Faker;

$factory->define(UnityInsight::class, function (Faker $faker) {
    return [
        'date'        => $faker->date(),
        'views'       => $faker->numberBetween(0, 1000),
        'clicks'      => $faker->numberBetween(0, 1000),
        'spend'       => $faker->numberBetween(0, 100),
        'installs'    => $faker->numberBetween(0, 100),
        'app_id'      => fn () => factory(\App\UnityApp::class)->create(),
        'campaign_id' => fn ($insight) => factory(\App\UnityCampaign::class)->create(['app_id' => $insight['app_id']]),
    ];
});
