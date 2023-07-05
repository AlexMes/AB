<?php

namespace App\Reports\BuyersPerformanceStats;

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
        self::OFFER,
        self::RK_COUNT,
        self::IMPRESSIONS,
        self::CLICKS,
        self::CPM,
        self::CPC,
        self::CTR,
        self::BCLICKS,
        self::LPVIEWS,
        self::LPCLICKS,
        self::CRPL,
        self::CRLP,
        self::CR,
        self::LEADS,
        self::BLEADS,
        self::FTD,
        self::FTD_PERCENT,
        self::FTD_COST,
        self::BFTD_PERCENT,
        self::REVENUE,
        self::CPL,
        self::BCPL,
        self::COST,
        self::PROFIT,
        self::ROI,
    ];


    public const NAME                   = 'name';
    public const OFFER                  = 'offer';
    public const IMPRESSIONS            = 'impressions';
    public const CLICKS                 = 'clicks';
    public const CPM                    = 'cpm';
    public const CPC                    = 'cpc';
    public const CTR                    = 'ctr';
    public const BCPL                   = 'b.cpl';
    public const BLEADS                 = 'b.leads';
    public const CRPL                   = 'LP CTR';
    public const CRLP                   = 'Offer CR';
    public const LPVIEWS                = 'LP Views';
    public const LPCLICKS               = 'LP Clicks';
    public const LEADS                  = 'leads';
    public const FTD                    = 'ftd';
    public const FTD_PERCENT            = 'ftd %';
    public const BFTD_PERCENT           = 'b. ftd %';
    public const REVENUE                = 'rev';
    public const CPL                    = 'fb.cpl';
    public const COST                   = 'cost';
    public const PROFIT                 = 'profit';
    public const ROI                    = 'roi';
    public const CR                     = 'CR';
    public const BCLICKS                = 'b.clicks';
    public const RK_COUNT               = 'РК';
    public const FTD_COST               = 'FTD Cost';

    /**
     * Get headings related to Facebook
     *
     * @return array
     */
    public static function fbKeys(): array
    {
        return [
            self::LEADS,
            self::FTD_PERCENT,
            self::CPL,
        ];
    }
}
