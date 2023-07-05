<?php

namespace App\Reports\MonthlyOfficesNOA;

class Fields
{
    public const NAME           = 'name';
    public const LEADS          = 'leads';
    public const NOA            = 'noa';
    public const DUPLICATES     = 'duplicates';

    public const ALL = [
        self::NAME,
        self::LEADS,
        self::NOA,
        self::DUPLICATES
    ];
}
