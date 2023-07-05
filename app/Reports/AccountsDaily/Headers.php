<?php

namespace App\Reports\AccountsDaily;

/**
 * Class Headers
 * Provides headers for daily report
 *
 * @package App\Reports\AccountsDaily
 */
class Headers
{
    public const ALL = [
        self::DATE,
        self::ACCOUNT,
        self::IMPRESSIONS,
        self::CLICKS,
        self::CPM,
        self::CPC,
        self::CTR,
        self::CRPL,
        self::CRLP,
        self::LEADS,
        self::FTD,
        self::FTD_PERCENT,
        self::REVENUE,
        self::CPL,
        self::COST,
        self::PROFIT,
        self::ROI,
    ];


    public const DATE                   = 'date';
    public const ACCOUNT                = 'acc';
    public const IMPRESSIONS            = 'impressions';
    public const CLICKS                 = 'clicks';
    public const CPM                    = 'cpm';
    public const CPC                    = 'cpc';
    public const CTR                    = 'ctr';
    public const CRPL                   = 'cr/pl';
    public const CRLP                   = 'cr/lp';
    public const LEADS                  = 'leads';
    public const FTD                    = 'ftd';
    public const FTD_PERCENT            = 'ftd %';
    public const REVENUE                = 'rev';
    public const CPL                    = 'cpl';
    public const COST                   = 'cost';
    public const PROFIT                 = 'profit';
    public const ROI                    = 'roi';
}
