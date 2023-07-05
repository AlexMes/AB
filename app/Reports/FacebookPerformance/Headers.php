<?php

namespace App\Reports\FacebookPerformance;

/**
 * Class Headers
 * Provides headers for daily report
 *
 * @package App\Reports\AccountsDaily
 */
class Headers
{
    public const ALL = [
        self::NAME,
        self::RK_COUNT,
        self::IMPRESSIONS,
        self::CLICKS,
        self::CPM,
        self::CPC,
        self::CTR,
        self::LPVIEWS,
        self::LPCLICKS,
        self::LEADS,
        self::BLEADS,
        self::CPL,
        self::COST,
    ];


    public const NAME                   = 'name';
    public const IMPRESSIONS            = 'impressions';
    public const CLICKS                 = 'clicks';
    public const CPM                    = 'cpm';
    public const CPC                    = 'cpc';
    public const CTR                    = 'ctr';
    public const BLEADS                 = 'b.leads';
    public const LPVIEWS                = 'LP Views';
    public const LPCLICKS               = 'LP Clicks';
    public const LEADS                  = 'leads';
    public const CPL                    = 'fb.cpl';
    public const COST                   = 'cost';
    public const RK_COUNT               = 'РК';

    /**
     * @param string|null $level
     *
     * @return array
     */
    public static function forLevel(?string $level)
    {
        return self::ALL;
    }

    /**
     * Get headings related to Facebook
     *
     * @return array
     */
    public static function fbKeys(): array
    {
        return [
            self::LEADS,
            self::CPL,
        ];
    }
}
