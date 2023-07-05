<?php

namespace App\Enums;

use App\Services\SmsEpochta;

final class SmsService
{
    public const EPOCHTA = 'epochta';

    public const LIST = [
        self::EPOCHTA => SmsEpochta::class,
    ];

    public const DRIVERS = [
        self::EPOCHTA => [
            'name'   => 'E-pochta',
            'driver' => SmsEpochta::class,
            'config' => [
                'url'         => 'http://api.myatompark.com/sms/3.0',
                'key_private' => '',
                'key_public'  => '',
                'sender'      => '',
                'test_mode'   => false,
                'cost'        => 4,
            ],
        ],
    ];
}
