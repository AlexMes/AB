<?php

return [
    'token'    => env('TELEGRAM_TOKEN', null),
    'channels' => [
        'devs'   => env('TELEGRAM_CHAT_DEVS'),
        'buyers' => env('TELEGRAM_CHAT_BUYERS'),
    ]
];
