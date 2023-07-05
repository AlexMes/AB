<?php

namespace App\Reports\TelegramPerformance;

/**
 * Class Headers
 * Provides headers for telegram performance report
 *
 * @package App\Reports\TelegramPerformance
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
        self::BLEADS,
        self::FTD,
        self::BFTD_PERCENT,
        self::REVENUE,
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
    public const FTD                    = 'ftd';
    public const BFTD_PERCENT           = 'b. ftd %';
    public const REVENUE                = 'rev';
    public const COST                   = 'cost';
    public const PROFIT                 = 'profit';
    public const ROI                    = 'roi';
    public const CR                     = 'CR';
    public const BCLICKS                = 'b.clicks';
    public const RK_COUNT               = 'РК';

    /**
     * @param string|null $level
     *
     * @return array
     */
    public static function forLevel(?string $level)
    {
        if ($level === Report::LEVEL_CAMPAIGN) {
            return self::ALL;
        }

        return array_diff(
            self::ALL,
            [/*self::CRPL,self::CRLP,self::CR, self::BLEADS,self::LPCLICKS,self::LPVIEWS,self::BCPL,self::BFTD_PERCENT,self::BCLICKS*/]
        );
    }
}
