<?php

namespace App\Reports\OfferStats;

/**
 * Class Headers
 * Provides headers for offers stats
 *
 * @package App\Reports\OfferStats
 */
class Headers
{
    public const ALL = [
        self::OFFER,
        self::BLEADS,
        self::DEPOSITS,
        self::FTD_PERCENT,
        self::CPL,
        self::PROFIT,
        self::REVENUE,
        self::COST,
        self::ROI
    ];

    public const OFFER        = 'OFFER';
    public const BLEADS       = 'LEADS';
    public const DEPOSITS     = 'DEPOSITS';
    public const PROFIT       = 'PROFIT';
    public const REVENUE      = 'REVENUE';
    public const ROI          = 'ROI';
    public const FTD_PERCENT  = 'FTD %';
    public const CPL          = 'CPL';
    public const COST         = 'COST';
}
