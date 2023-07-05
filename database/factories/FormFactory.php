<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Integrations\Form::class, function (Faker $faker) {
    return [
        'name'    => $faker->word,
        'url'     => $faker->url,
        'form_id' => 1,
    ];
});
