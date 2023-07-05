<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PlatformInsights
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
 * @property string|null $device_platform
 * @property string|null $publisher_platform
 * @property string|null $impression_device
 * @property string|null $platform_position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $frequency
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereActions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereCpc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereCpl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereCpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereCtr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereDevicePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereImpressionDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereLeadsCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereLinkClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights wherePlatformPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights wherePublisherPlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereReach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformInsights whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlatformInsights extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table   = 'facebook_platform_insights';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * No incrementing whatever.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * No primary key
     *
     * @var null
     */
    protected $primaryKey = null;


    /**
    * Cast attributes to Carbon instances
    *
    * @var array
    */
    protected $dates = [
        'date'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'actions' => 'json'
    ];
}
