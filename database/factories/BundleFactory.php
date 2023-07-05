<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bundle;
use Faker\Generator as Faker;

$factory->define(Bundle::class, function (Faker $faker) {
    return [
        'offer_id'      => factory(\App\Offer::class),
        'utm_campaign'  => $faker->sentence(3),
        'examples'      => $faker->text(),
        'geo'           => $faker->sentence(),
        'age'           => $faker->randomNumber(2),
        'gender'        => $faker->randomElement(['Мужской', 'Женский']),
        'interests'     => $faker->text(),
        'device'        => $faker->randomElement(['Мобильный', 'ПК']),
        'platform'      => $faker->randomElement(['Facebook', 'Instagram']),
        'ad'            => $faker->url,
        'prelend_link'  => $faker->url,
        'lend_link'     => $faker->url,
        'utm_source'    => $faker->sentence(2),
        'utm_content'   => $faker->sentence(2),
        'utm_term'      => $faker->sentence(2),
        'utm_medium'    => $faker->sentence(2),
        'title'         => $faker->words(2, true),
        'description'   => $faker->sentences(2, true),
        'text'          => $faker->text(),
    ];
});
