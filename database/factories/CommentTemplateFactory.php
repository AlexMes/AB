<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Facebook\CommentTemplate::class, function (Faker $faker) {
    return [
        'message' => $faker->paragraph,
    ];
});
