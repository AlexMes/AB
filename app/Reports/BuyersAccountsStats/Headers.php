<?php

namespace App\Reports\BuyersAccountsStats;

/**
 * Class Headers
 * Provides headers for daily report
 *
 * @package App\Reports\AccountsDaily
 */
class Headers
{
    public const ALL = [
        self::BUYER,
        self::ACTIVE,
        self::SPEND,
        self::AVERAGE_SPEND,
        self::DISABLED,
        self::UNSETTLED,
        self::PENDING_RISK_REVIEW,
        self::PENDING_SETTLEMENT,
        self::IN_GRACE_PERIOD,
        self::OTHERS,
        self::TOTAL,
        self::TOTAL_COST,
        self::BALANCE,
    ];

    public const BUYER                                = 'BUYER';
    public const ACTIVE                               = 'ACTIVE';
    public const TOTAL_COST                           = 'TOTAL COST';
    public const AVERAGE_SPEND                        = 'AVERAGE SPEND';
    public const DISABLED                             = 'DISABLED';
    public const UNSETTLED                            = 'UNSETTLED';
    public const PENDING_RISK_REVIEW                  = 'PENDING RISK REVIEW';
    public const PENDING_SETTLEMENT                   = 'PENDING SETTLEMENT';
    public const IN_GRACE_PERIOD                      = 'IN GRACE PERIOD';
    public const OTHERS                               = 'OTHERS';
    public const TOTAL                                = 'TOTAL';
    public const BALANCE                              = 'BALANCE';
    public const SPEND                                = 'SPEND';
}
