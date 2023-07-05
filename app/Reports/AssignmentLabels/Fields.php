<?php

namespace App\Reports\AssignmentLabels;

class Fields
{
    public const UTM_SOURCE     = 'UTM Source';
    public const UTM_CAMPAIGN   = 'UTM Campaign';
    public const UTM_CONTENT    = 'UTM Content';
    public const LEADS          = 'leads';
    public const UNDERAGE       = 'underage';
    public const WRONGRESIDENCE = 'wrongResidence';
    public const SOUTHGUEST     = 'southGuest';
    public const NOREGISTRATION = 'noRegistration';

    public const ALL = [
        self::UTM_SOURCE,
        self::UTM_CAMPAIGN,
        self::UTM_CONTENT,
        self::LEADS,
        self::UNDERAGE,
        self::WRONGRESIDENCE,
        self::SOUTHGUEST,
        self::NOREGISTRATION,
    ];
}
