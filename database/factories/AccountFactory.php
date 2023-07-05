<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facebook\Account;
use App\Facebook\Profile;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'id'             => "act_{$faker->uuid}",
        'account_id'     => function ($account) {
            return preg_replace('/act_/', '', $account['id']);
        },
        'name'           => $faker->word,
        'age'            => rand(18, 100),
        'account_status' => 1,
        'amount_spent'   => rand(0, 1000),
        'balance'        => rand(0, 1000),
        'currency'       => 'USD',
        'profile_id'     => function () {
            return factory(Profile::class)->create()->id;
        },
    ];
});
