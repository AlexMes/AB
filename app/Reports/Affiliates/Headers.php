<?php

namespace App\Reports\Affiliates;

/**
 * Class Headers
 * Provides headers for daily report
 *
 * @package App\Reports\DailyOpt
 */
class Headers
{
    public const AFFILIATE                     = 'affiliate';
    public const OFFER                         = 'offer';
    public const LEADS                         = 'leads';
    public const LEADS_VALID                   = 'valid';
    public const LEAD_INVALID                  = 'invalid';
    public const FTD                           = 'ftd';
    public const FTD_PERCENT                   = 'ftd %';
    public const REVENUE                       = 'rev';
    public const CPL                           = 'cpl';
    public const CPA                           = 'cpa';
    public const COST                          = 'cost';
    public const PROFIT                        = 'profit';
    public const ROI                           = 'roi';
    public const STATUS_NEW                    = 'Новый';
    public const STATUS_REJECT                 = 'Отказ';
    public const STATUS_ON_CLOSER              = 'В работе у клоузера';
    public const STATUS_NO_ANSWER              = 'Нет ответа';
    public const STATUS_FORCE_CALL             = 'Дозвонится';
    public const STATUS_DEMO                   = 'Демонстрация';
    public const STATUS_DEPOSITS               = 'Депозит';
    public const STATUS_CALLBACK               = 'Перезвон';
    public const STATUS_DOUBLE                 = 'Дубль';
    public const STATUS_RESERVE                = 'Резерв';
    public const STATUS_WRING_NB               = 'Неверный номер';


    public const LIST = [
        self::AFFILIATE,
        self::OFFER,
        self::LEADS,
        self::LEADS_VALID,
        self::LEAD_INVALID,
        self::FTD,
        self::FTD_PERCENT,
        self::REVENUE,
        self::CPL,
        self::CPA,
        self::COST,
        self::PROFIT,
        self::ROI,
        self::STATUS_NEW,
        self::STATUS_REJECT,
        self::STATUS_ON_CLOSER,
        self::STATUS_NO_ANSWER,
        self::STATUS_FORCE_CALL,
        self::STATUS_DEMO,
        self::STATUS_DEPOSITS,
        self::STATUS_CALLBACK,
        self::STATUS_DOUBLE,
        self::STATUS_RESERVE,
        self::STATUS_WRING_NB,
    ];
}
