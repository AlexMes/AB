<?php

namespace App\Reports\MonthlyOfficeCR;

class Fields
{
    public const NAME   = 'name';
    public const LEADS  = 'leads';
    public const FDTS   = 'fdts';

    public const ALL = [
        self::NAME,
        self::LEADS,
        self::FDTS
    ];
}
