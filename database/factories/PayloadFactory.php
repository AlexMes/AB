<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Integrations\Payload::class, function (Faker $faker) {
    return [
        'form_id'          => function () {
            return factory(App\Integrations\Form::class)->create();
        },
        'offer_id'         => function () {
            return factory(App\Offer::class)->create();
        },
        'lead_id'          => function () {
            return factory(App\Lead::class)->create();
        },
        'responseContents' => null
    ];
});


$factory->state(\App\Integrations\Payload::class, 'duplicate_response', function (Faker $faker) {
    return [
        'responseContents' => "{\"success\":true,\"message\":\"14564\",\"fields\":[{\"name\":\"ID\",\"unique\":\"id1\",\"type\":\"id_user\",\"params\":null},{\"name\":\"\u0418\u043c\u044f\",\"unique\":\"imya\",\"type\":\"text\",\"params\":{\"text_search\":\"1\"}},{\"name\":\"\u0424\u0430\u043c\u0438\u043b\u0438\u044f\",\"unique\":\"familiya1\",\"type\":\"text\",\"params\":{\"text_search\":\"1\"}},{\"name\":\"\u0422\u0435\u043b\u0435\u0444\u043e\u043d\",\"unique\":\"telefon\",\"type\":\"callbyphone\",\"params\":{\"duplicate\":\"1\"}},{\"name\":\"utm_campaign\",\"unique\":\"utmcampaign\",\"type\":\"text\",\"params\":null},{\"name\":\"utm_term\",\"unique\":\"utmterm\",\"type\":\"text\",\"params\":null},{\"name\":\"utm_medium\",\"unique\":\"utmmedium\",\"type\":\"text\",\"params\":null}]}"
    ];
});

$factory->state(\App\Integrations\Payload::class, 'normal_response', function (Faker $faker) {
    return [
        'responseContents' => "{\"success\":true,\"message\":\"14564\",\"fields\":[{\"name\":\"ID\",\"unique\":\"id1\",\"type\":\"id_user\",\"params\":null},{\"name\":\"\u0418\u043c\u044f\",\"unique\":\"imya\",\"type\":\"text\",\"params\":{\"text_search\":\"1\"}},{\"name\":\"\u0424\u0430\u043c\u0438\u043b\u0438\u044f\",\"unique\":\"familiya1\",\"type\":\"text\",\"params\":{\"text_search\":\"1\"}},{\"name\":\"\u0422\u0435\u043b\u0435\u0444\u043e\u043d\",\"unique\":\"telefon\",\"type\":\"callbyphone\",\"params\":null},{\"name\":\"utm_campaign\",\"unique\":\"utmcampaign\",\"type\":\"text\",\"params\":null},{\"name\":\"utm_term\",\"unique\":\"utmterm\",\"type\":\"text\",\"params\":null},{\"name\":\"utm_medium\",\"unique\":\"utmmedium\",\"type\":\"text\",\"params\":null}]}"
    ];
});
