<?php

namespace App;

/**
 * App\AgeInsights
 *
 * @property \Illuminate\Support\Carbon $date
 * @property string $account_id
 * @property string $campaign_id
 * @property int|null $offer_id
 * @property int $reach
 * @property int $impressions
 * @property string $spend
 * @property string $cpm
 * @property string $cpc
 * @property string $ctr
 * @property string $cpl
 * @property int $clicks
 * @property int $leads_cnt
 * @property string|null $link_clicks
 * @property array|null $actions
 * @property string|null $adset_id
 * @property string|null $ad_id
 * @property string $age
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $frequency
 * @property-read \App\Facebook\Account $account
 * @property-read \App\Facebook\Ad|null $ad
 * @property-read \App\Facebook\AdSet|null $adset
 * @property-read \App\Facebook\Campaign $campaign
 * @property-read \App\Offer|null $offer
 *
 * @method static Builder|Insights allowedOffers()
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights query()
 * @method static Builder|Insights visible()
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereActions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereCpc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereCpl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereCpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereCtr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereLeadsCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereLinkClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereReach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgeInsights whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AgeInsights extends Insights
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'facebook_cached_age_insights';
}
