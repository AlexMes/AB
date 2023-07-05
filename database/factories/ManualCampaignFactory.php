<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualCampaign;
use Faker\Generator as Faker;

$factory->define(ManualCampaign::class, function (Faker $faker) {
    return [
        'id'            => $faker->uuid,
        'account_id'    => fn () => factory(\App\ManualAccount::class)->create()->account_id,
        'name'          => $faker->word,
        'bundle_id'     => factory(\App\ManualBundle::class),
    ];
});
