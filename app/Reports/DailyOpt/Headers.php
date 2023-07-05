<?php

namespace App\Reports\DailyOpt;

/**
 * Class Headers
 * Provides headers for daily report
 *
 * @package App\Reports\DailyOpt
 */
class Headers
{
    public const DATE                         = 'date';
    public const DESK                         = 'desk';
    public const OFFER                        = 'offer';
    public const CLICKS                       = 'clicks';
    public const CPM                          = 'cpm';
    public const CPC                          = 'cpc';
    public const CTR                          = 'ctr';
    public const CRPL                         = 'cr/pl';
    public const CRLP                         = 'cr/lp';
    public const CR                           = 'offer cr';
    public const LEADS                        = 'leads';
    public const NA                           = 'na';
    public const FTD                          = 'ftd';
    public const FTD_PERCENT                  = 'ftd %';
    public const REVENUE                      = 'rev';
    public const CPL                          = 'cpl';
    public const COST                         = 'cost';
    public const PROFIT                       = 'profit';
    public const ROI                          = 'roi';
    public const BLEADS                       = 'b.leads';
    public const BCPL                         = 'cpl';
    public const BCOST                        = 'cost';

    public const FULL = [
        self::DATE,
        self::DESK,
        self::OFFER,
        self::CLICKS,
        self::CPM,
        self::CPC,
        self::CTR,
        self::CRPL,
        self::CRLP,
        self::CR,
        self::LEADS,
        self::FTD,
        self::FTD_PERCENT,
        self::REVENUE,
        self::BCPL,
        self::BCOST,
        self::PROFIT,
        self::ROI,
    ];
}
