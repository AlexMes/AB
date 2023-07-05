<?php

namespace App\Reports\FacebookPerformance;

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
    public const NAME                = 'name';
    public const IMPRESSIONS         = 'impressions';
    public const CLICKS              = 'clicks';
    public const CPM                 = 'cpm';
    public const CPC                 = 'cpc';
    public const CTR                 = 'ctr';
    public const LEADS               = 'leads_cnt';
    public const CPL                 = 'cpl';
    public const COST                = 'cost';
    public const BLEADS              = 'bleads';
    public const LPCLICKS            = 'lpViews';
    public const LPVIEWS             = 'lpClicks';
    public const RK_COUNT            = 'rkCount';


    /**
     * Get Facebook keys to hide
     *
     * @return array
     */
    public static function fbKeys(): array
    {
        return  [
            self::LEADS,
            self::CPL,
        ];
    }
}
