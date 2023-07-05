<?php

namespace App\Reports\BuyersPerformanceStats;

/**
 * Class Fields
 *
 * Provides fields for daily report
 *
 * @package App\Reports\Daily
 */
class Fields
{
    public const DATE                = 'date';
    public const OFFER               = 'offer';
    public const NAME                = 'name';
    public const IMPRESSIONS         = 'impressions';
    public const CLICKS              = 'clicks';
    public const CPM                 = 'cpm';
    public const CPC                 = 'cpc';
    public const CTR                 = 'ctr';
    public const CRPL                = 'crpl';
    public const CRLP                = 'crlp';
    public const LEADS               = 'leads_cnt';
    public const FTD                 = 'ftd_count';
    public const FTD_PERCENT         = 'ftd_percent';
    public const REVENUE             = 'rev';
    public const CPL                 = 'cpl';
    public const COST                = 'cost';
    public const PROFIT              = 'profit';
    public const ROI                 = 'roi';
    public const BLEADS              = 'bleads';
    public const CR                  = 'CR';
    public const LPCLICKS            = 'lpViews';
    public const LPVIEWS             = 'lpClicks';
    public const BCPL                = 'binomCpl';
    public const BFTD_PERCENT        = 'bftdPercent';
    public const BCLICKS             = 'binomClicks';
    public const RK_COUNT            = 'rkCount';
    public const FTD_COST            = 'ftdCost';


    /**
     * Get Facebook keys to hide
     *
     * @return array
     */
    public static function fbKeys(): array
    {
        return  [
            self::LEADS,
            self::FTD_PERCENT,
            self::CPL,
        ];
    }
}
