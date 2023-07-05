<?php

namespace App\Reports\AccountsBanned;

/**
 * Class Headers
 * Provides headers for report
 *
 * @package App\Reports\AccountsBanned
 */
class Headers
{
    public const BY_SPEND = [
        self::NAME,
        self::RK_PERCENT,
        self::RK_COUNT,
        self::SPEND,
        self::LIFETIME,
        self::AVG_SPEND,
    ];

    public const GROUP = [
        self::NAME,
        self::RK_COUNT,
        self::SPEND,
        self::LIFETIME,
        self::AVG_SPEND,
    ];

    public const DEFAULT = [
        self::NAME,
        self::BUYERS,
        self::RK_COUNT,
        self::SPEND,
        self::LIFETIME,
        self::AVG_SPEND,
    ];

    public const NAME                = 'name';
    public const RK_COUNT            = 'PK';
    public const LIFETIME            = 'lifetime';
    public const SPEND               = 'spend';
    public const AVG_SPEND           = 'avg spend';
    public const BUYERS              = 'buyer';
    public const RK_PERCENT          = 'RK%';

    /**
     * @param string $group
     *
     * @return array
     */
    public static function build(string $group)
    {
        switch ($group) {
            case 'spend':
                return  self::BY_SPEND;
            case 'group':
                return self::GROUP;
            default:
                return self::DEFAULT;
        }
    }
}
