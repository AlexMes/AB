<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\Account;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'account_id' => $faker->uuid,
        'name'       => 'Account - ' . $faker->words(2, true),
    ];
});
