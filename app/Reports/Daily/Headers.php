<?php

namespace App\Reports\Daily;

/**
 * Class Headers
 * Provides headers for daily report
 *
 * @package App\Reports\Daily
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
    public const NA_PERCENT                   = 'na%';
    public const REJECTS                      = 'rejects';
    public const REJECTS_PERCENT              = 'rejects%';
    public const WRONG_NB                     = 'wrong nb';
    public const WRONG_NB_PERCENT             = 'wrong nb %';
    public const DEMO                         = 'demo';
    public const DEMO_PERCENT                 = 'demo %';
    public const FTD                          = 'ftd';
    public const FTD_PERCENT                  = 'ftd %';
    public const DEMO_TO_FTD                  = 'demo to ftd %';
    public const REVENUE                      = 'rev';
    public const CPL                          = 'cpl';
    public const COST                         = 'cost';
    public const PROFIT                       = 'profit';
    public const ROI                          = 'roi';
    public const BLEADS                       = 'b.leads';
    public const BCPL                         = 'cpl';
    public const BCOST                        = 'cost';

    public const SIMPLE = [
        self::DATE,
        self::OFFER,
        self::CLICKS,
        self::CPM,
        self::CPC,
        self::CTR,
        self::CR,
        self::LEADS,
        self::BLEADS,
        self::FTD,
        self::FTD_PERCENT,
        self::REVENUE,
        self::CPL,
        self::BCPL,
        self::COST,
        self::PROFIT,
        self::ROI,
    ];

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
        //self::BLEADS,
        //self::NA,
        //self::NA_PERCENT,
        //self::REJECTS,
        //self::REJECTS_PERCENT,
        //self::WRONG_NB,
        //self::WRONG_NB_PERCENT,
        //self::DEMO,
        //self::DEMO_PERCENT,
        self::FTD,
        self::FTD_PERCENT,
        //self::DEMO_TO_FTD,
        self::REVENUE,
        //self::CPL,
        //self::COST,
        self::BCPL,
        self::BCOST,
        self::PROFIT,
        self::ROI,
    ];
}
