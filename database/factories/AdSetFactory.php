<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facebook\Campaign;
use Faker\Generator as Faker;

$factory->define(\App\Facebook\AdSet::class, function (Faker $faker) {
    return [
        'id'          => $faker->word,
        'name'        => $faker->name,
        'campaign_id' => factory(Campaign::class),
        'account_id'  => function () {
            return factory(\App\Facebook\Account::class)->create()->account_id;
        },
    ];
});
