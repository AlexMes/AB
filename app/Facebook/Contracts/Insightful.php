<?php

namespace App\Facebook\Contracts;

/**
 * Interface Insightful
 *
 * @property string $id
 * @property string $name
 * @property string $status
 * @property string $account
 * @property string $dailyBudget
 *
 * @package App\Facebook\Contracts
 */
interface Insightful
{
    public const MODES = [
        self::MODE_ACCOUNT,self::MODE_CAMPAIGN,self::MODE_ADSET,self::MODE_AD,
    ];
    public const MODE_ACCOUNT  = 'account';
    public const MODE_CAMPAIGN = 'campaign';
    public const MODE_ADSET    = 'adset';
    public const MODE_AD       = 'ad';

    public const FIELDS = [
        'reach',
        'frequency',
        'impressions',
        'spend',
        'actions',
        'cost_per_action_type',
        'cpm',
        'clicks',
        'cpc',
        'ctr',
        'website_ctr',
    ];

    /**
     * Get account access token
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Should return collection of insights
     *
     * @param array $fields
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     */
    public function insights(array $fields = [], array $params = []);
}
