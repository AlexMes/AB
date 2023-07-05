<?php

namespace App\Reports\CampaignStats;

class Fields
{
    public const CAMPAIGN    = 'campaign';
    public const IMPRESSIONS = 'impressions';
    public const CLICKS      = 'clicks';
    public const CPM         = 'cpm';
    public const CPC         = 'cpc';
    public const CTR         = 'ctr';
    public const LEADS       = 'leads';
    public const CPL         = 'cpl';
    public const COST        = 'cost';

    public const ALL = [
        self::CAMPAIGN,
        self::IMPRESSIONS,
        self::CLICKS,
        self::CPM,
        self::CPC,
        self::CTR,
        self::LEADS,
        self::CPL,
        self::COST
    ];
}
