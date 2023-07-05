<?php

namespace App;

use Illuminate\Support\Facades\Storage;

class AdsBoard
{
    /**
     * Application version
     *
     * @var string
     */
    public static $version = '0.0.0';

    public const QUEUE_DEFAULT       = 'default';
    public const QUEUE_FACEBOOK      = 'facebook';
    public const QUEUE_MONITORING    = 'monitoring';
    public const QUEUE_NOTIFICATIONS = 'notifications';
    public const QUEUE_SMS           = 'sms';
    public const QUEUE_CLEANING      = 'cleaning';
    public const QUEUE_IMPORTS       = 'imports';
    public const QUEUE_BINOM         = 'binom';
    public const QUEUE_INSIGHTS      = 'insights';
    public const QUEUE_GOOGLE        = 'google';
    public const QUEUE_STOP          = 'halter';
    public const QUEUE_POURER        = 'pourer';
    public const QUEUE_POSTBACKS     = 'postbacks';
    public const QUEUE_SINGLETON     = 'singleton';
    public const QUEUE_VK            = 'vk';
    public const QUEUE_UNITY         = 'unity';
    public const QUEUES              = [];

    public const DELUGE_OFFICE_IPS = [
        '89.163.210.165',
        '185.212.44.63',
        '212.83.135.112',
        '185.158.251.48',
        '185.158.251.111',
        '185.158.251.166',
        '31.214.157.231',
        '49.12.189.77',
        '45.153.230.228',
        '170.130.55.68',
        '78.46.177.94',
        '45.155.250.167',
        '185.219.221.80',
        '79.133.120.50',
    ];

    public const PRIV = [];

    /**
     * Jobs for queues, that is vital for business
     * i.e. stopping unprofitable adsets
     */
    public const QUEUES_CRIT = [
        self::QUEUE_STOP,
    ];

    /**
     * Internal queues for notifications,
     * monitoring, broadcasting, cleanups, etc.
     */
    public const QUEUES_INTERNAL = [
        self::QUEUE_DEFAULT,
        self::QUEUE_NOTIFICATIONS,
        self::QUEUE_IMPORTS,
        self::QUEUE_MONITORING,
        self::QUEUE_POURER,
    ];

    /**
     * Queues for caching jobs only
     */
    public const QUEUES_CACHE = [
        self::QUEUE_INSIGHTS,
        self::QUEUE_FACEBOOK,
        self::QUEUE_BINOM,
        self::QUEUE_POSTBACKS,
        self::QUEUE_VK,
        self::QUEUE_UNITY,
    ];

    /**
     * Queues for services and 3-rd party
     * integartions
     */
    public const QUEUES_SERVICES = [
        self::QUEUE_GOOGLE,
        self::QUEUE_SMS,
        self::QUEUE_CLEANING,
    ];

    /**
     * Get developers telegram channel
     *
     * @return string|null
     */
    public static function devsChannel()
    {
        return config('telegram.channels.devs');
    }

    /**
     * Get team emails, that must have access
     * to all spreadsheets created from app
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return array
     */
    public static function teamEmails(): array
    {
        return [
            'supp@szlsrz.com',
            'sdal_supp@szlsrz.com',
            'adm@szlsrz.com',
            json_decode(Storage::get('credentials.json'), true)['client_email'],
        ];
    }

    /**
     * Send report
     *
     * @param $message
     */
    public static function report(string $message)
    {
        app(\App\Bot\Telegram::class)
            ->say($message)
            ->to('-1001812065929')
            ->send();
    }
}
