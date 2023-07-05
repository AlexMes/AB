<?php

namespace App\Reports\OfficePerformanceCopy;

/**
 * Class Headers
 * Provides headers
 *
 */
class Headers
{
    public const ALL = [
        self::OFFICE,
        self::OFFER,
        self::UTM_SOURCE,
        self::LEADS,
        self::DEPOSITS,
        self::CONVERSION,
        self::LATE_DEPOSITS,
        self::LATE_CONVERSION,
        self::TOTAL_DEPOSITS,
        self::TOTAL_CONVERSION,
    ];

    public const OFFICE           = 'office';
    public const OFFER            = 'offer';
    public const UTM_SOURCE       = 'UTM source';
    public const LEADS            = 'leads';
    public const DEPOSITS         = 'ftd';
    public const CONVERSION       = 'ftd %';
    public const LATE_DEPOSITS    = 'late ftd';
    public const LATE_CONVERSION  = 'late ftd %';
    public const TOTAL_DEPOSITS   = 'total ftd';
    public const TOTAL_CONVERSION = 'total ftd %';
}
