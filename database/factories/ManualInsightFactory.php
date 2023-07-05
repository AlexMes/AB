<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualInsight;
use Faker\Generator as Faker;

$factory->define(ManualInsight::class, function (Faker $faker) {
    return [
        'date'          => $faker->date(),
        'account_id'    => fn () => factory(\App\ManualAccount::class)->create()->account_id,
        'campaign_id'   => function ($insight) {
            return factory(\App\ManualCampaign::class)->create(['account_id' => $insight['account_id']])->id;
        },
        'impressions'   => $faker->numberBetween(0, 1000),
        'clicks'        => $faker->numberBetween(0, 1000),
        'spend'         => $faker->numberBetween(0, 100),
        'leads_cnt'     => $faker->numberBetween(0, 100),
    ];
});
