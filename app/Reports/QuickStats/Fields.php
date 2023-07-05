<?php

namespace App\Reports\QuickStats;

class Fields
{
    public const  OFFER          = 'offer';
    public const  IMPRESSIONS    = 'impressions';
    public const  CLICKS         = 'clicks';
    public const  CPM            = 'cpm';
    public const  CPC            = 'cpc';
    public const  CTR            = 'ctr';
    public const  LEADS          = 'fb leads';
    public const  CPL            = 'fb cpl';
    public const  COST           = 'cost';
    public const OFFER_CR        = 'offer cr';
    public const BINOM_LEADS     = 'b leads';
    public const BINOM_CPL       = 'b cpl';

    public const ALL = [
        self::OFFER,
        self::IMPRESSIONS,
        self::CLICKS,
        self::CPM,
        self::CPC,
        self::CTR,
        self::LEADS,
        self::BINOM_LEADS,
        self::OFFER_CR,
        self::CPL,
        self::BINOM_CPL,
        self::COST,
    ];
}
