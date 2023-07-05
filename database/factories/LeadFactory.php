<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Lead::class, function (Faker $faker) {
    return [
        'domain'       => $faker->domainName,
        'firstname'    => $faker->firstName,
        'lastname'     => $faker->lastName,
        'email'        => $faker->email,
        'phone'        => $faker->phoneNumber,
        'ip'           => $faker->ipv4,
        'form_type'    => null,
        'utm_source'   => '',
        'utm_content'  => '',
        'utm_campaign' => '',
        'utm_term'     => '',
        'utm_medium'   => '',
        'clickid'      => '',
    ];
});

$factory->state(\App\Lead::class, 'valid', fn (Faker $faker) => ['valid' => true]);
