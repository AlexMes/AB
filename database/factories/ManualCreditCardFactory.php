<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualCreditCard;
use Faker\Generator as Faker;

$factory->define(ManualCreditCard::class, function (Faker $faker) {
    return [
        'number'         => $this->faker->creditCardNumber(),
        'bank_name'      => $this->faker->words(2, true),
        'social_profile' => null,
        'buyer_id'       => factory(\App\User::class)->state('buyer'),
        'account_id'     => fn ($card) => factory(\App\ManualAccount::class)
            ->create(['user_id' => $card['buyer_id']])
            ->account_id,
    ];
});
