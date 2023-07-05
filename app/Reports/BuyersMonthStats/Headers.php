<?php

namespace App\Reports\BuyersMonthStats;

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
        self::LEADS,
        self::FTD,
        self::CPL,
        self::COST,
        self::PROFIT,
        self::ROI,
    ];

    public const BUYER             = 'BUYER';
    public const LEADS             = 'LEADS';
    public const FTD               = 'FTD';
    public const CPL               = 'CPL';
    public const COST              = 'COST';
    public const PROFIT            = 'PROFIT';
    public const ROI               = 'ROI';
}
