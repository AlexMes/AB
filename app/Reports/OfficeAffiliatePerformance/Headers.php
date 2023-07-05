<?php

namespace App\Reports\OfficeAffiliatePerformance;

/**
 * Class Headers
 * Provides headers
 *
 */
class Headers
{
    public const ALL = [
        self::AFFILIATE,
        self::OFFICE,
        self::OFFER,
        self::LEADS,
        self::DEPOSITS,
        self::CONVERSION,
        self::LATE_DEPOSITS,
        self::LATE_CONVERSION,
        self::TOTAL_DEPOSITS,
        self::TOTAL_CONVERSION,
        self::NEW,
        self::NO_ANSWER,
        self::NO_ANSWER_CONVERSION,
        self::INVALID,
        self::INVALID_CONVERSION,
    ];

    public const AFFILIATE              = 'affiliate';
    public const OFFICE                 = 'office';
    public const OFFER                  = 'offer';
    public const LEADS                  = 'leads';
    public const DEPOSITS               = 'ftd';
    public const CONVERSION             = 'ftd %';
    public const LATE_DEPOSITS          = 'late ftd';
    public const LATE_CONVERSION        = 'late ftd %';
    public const TOTAL_DEPOSITS         = 'total ftd';
    public const TOTAL_CONVERSION       = 'total ftd %';
    public const NEW                    = 'new';
    public const NO_ANSWER              = 'NA';
    public const NO_ANSWER_CONVERSION   = 'NA %';
    public const INVALID                = 'invalid';
    public const INVALID_CONVERSION     = 'invalid %';
}
