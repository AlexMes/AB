<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'sms' => env('SMS_SERVICE', null),

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],
    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'binom' => [
        'token' => env('BINOM_TOKEN', null),
        'url'   => env('BINOM_URL', null)
    ],
    'facebook' => [
        'token' => env('FB_APP_TOKEN')
    ],
    'smsclub' => [
        'alfa_name' => env('SMSCLUB_ALPHA', 'RUSMS'),
        'token'     => env('SMSCLUB_TOKEN'),
        'cost'      => env('SMSCLUB_COST', 0),
    ],
    'dadata' => [
        'token'  => env('DADATA_TOKEN'),
        'secret' => env('DADATA_SECRET')
    ],
    'ipapi' => [
        'token' => env('IPAPI_TOKEN', ''),
    ],
    'genderapi' => [
        'token' => env('GENDERAPI_TOKEN', ''),
    ],
    'smsprosto' => [
        'url'       => 'http://api.sms-prosto.ru',
        'login'     => env('SMS_PROSTO_LOGIN'),
        'password'  => env('SMS_PROSTO_PASSWORD'),
        'sender'    => env('SMS_PROSTO_SENDER'),
        'method'    => env('SMS_PROSTO_METHOD', 'push_msg'),
        'cost'      => env('SMS_PROSTO_COST', 0),
    ],
    'smsepochta'   => [
        'url'           => 'http://api.myatompark.com/sms/3.0',
        'key_private'   => env('SMS_EPOCHTA_KEY_PRIVATE', ''),
        'key_public'    => env('SMS_EPOCHTA_KEY_PUBLIC', ''),
        'sender'        => env('SMS_EPOCHTA_SENDER', ''),
        'test_mode'     => env('SMS_EPOCHTA_TEST', false),
        'cost'          => env('SMS_EPOCHTA_COST', 0),
    ],
    'google' => [
        'token' => env('GOOGLE_API_KEY')
    ],
    'forge' => [
        'token' => env('FORGE_TOKEN')
    ],
    'messagebird' => [
        'key' => env('MESSAGEBIRD_KEY')
    ],
    'pourer' => [
        'ip' => env('POURER_IP', '165.227.127.81')
    ]
];
