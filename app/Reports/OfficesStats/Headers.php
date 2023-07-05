<?php

namespace App\Reports\OfficesStats;

/**
 * Class Headers
 * Provides headers for office stats
 *
 * @package App\Reports\AccountsDaily
 */
class Headers
{
    public const ALL = [
        self::OFFICE,
        self::BLEADS,
        self::DEPOSITS,
        self::FTD_PERCENT,
        //        self::CPL,
        //        self::PROFIT,
        //        self::REVENUE,
        //        self::COST,
        //        self::ROI
    ];

    public const OFFICE                         = 'OFFICE';
    public const BLEADS                         = 'LEADS';
    public const DEPOSITS                       = 'DEPOSITS';
    public const PROFIT                         = 'PROFIT';
    public const REVENUE                        = 'REVENUE';
    public const ROI                            = 'ROI';
    public const FTD_PERCENT                    = 'FTD %';
    public const CPL                            = 'CPL';
    public const COST                           = 'COST';
}
