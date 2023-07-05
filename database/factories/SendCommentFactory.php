<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Facebook\SentComment::class, function (Faker $faker) {
    return [
        'profile_id' => factory(\App\Facebook\Profile::class),
        'ad_id'      => factory(\App\Facebook\Ad::class),
        'comment_id' => $faker->bankAccountNumber,
        'message'    => $faker->paragraph,
    ];
});
