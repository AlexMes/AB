<?php

namespace App\Notifications;

/**
 * Class NotificationSeverity
 *
 * @package App\Enums
 */
class NotificationSeverity
{
    /**
     * Define available severity levels for notifications.
     *
     * @var array
     */
    public const SEVERITIES = [self::SUCCESS, self::INFO, self::WARNING, self::ERROR];

    /**
     *
     * @var string
     */
    public const SUCCESS = 'success';

    /**
     *
     * @var string
     */
    public const INFO = 'info';

    /**
     *
     * @var string
     */
    public const WARNING = 'warning';

    /**
     *
     * @var string
     */
    public const ERROR = 'error';
}
