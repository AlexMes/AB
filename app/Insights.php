<?php

namespace App;

use App\Facebook\Account;
use App\Facebook\Ad;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Insights
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $link_clicks
 * @property array|null $actions
 * @property string|null $adset_id
 * @property string|null $ad_id
 * @property int|null $user_id
 * @property string $frequency
 * @property-read Account $account
 * @property-read Ad|null $ad
 * @property-read AdSet|null $adset
 * @property-read Campaign $campaign
 * @property-read \App\Offer|null $offer
 *
 * @method static Builder|Insights allowedOffers()
 * @method static Builder|Insights newModelQuery()
 * @method static Builder|Insights newQuery()
 * @method static Builder|Insights query()
 * @method static Builder|Insights visible()
 * @method static Builder|Insights whereAccountId($value)
 * @method static Builder|Insights whereActions($value)
 * @method static Builder|Insights whereAdId($value)
 * @method static Builder|Insights whereAdsetId($value)
 * @method static Builder|Insights whereCampaignId($value)
 * @method static Builder|Insights whereClicks($value)
 * @method static Builder|Insights whereCpc($value)
 * @method static Builder|Insights whereCpl($value)
 * @method static Builder|Insights whereCpm($value)
 * @method static Builder|Insights whereCreatedAt($value)
 * @method static Builder|Insights whereCtr($value)
 * @method static Builder|Insights whereDate($value)
 * @method static Builder|Insights whereFrequency($value)
 * @method static Builder|Insights whereImpressions($value)
 * @method static Builder|Insights whereLeadsCnt($value)
 * @method static Builder|Insights whereLinkClicks($value)
 * @method static Builder|Insights whereOfferId($value)
 * @method static Builder|Insights whereReach($value)
 * @method static Builder|Insights whereSpend($value)
 * @method static Builder|Insights whereUpdatedAt($value)
 * @method static Builder|Insights whereUserId($value)
 * @mixin \Eloquent
 */
class Insights extends Model
{
    use HasVisibilityScope;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'facebook_cached_insights';

    /**
     * Guarded model attributes
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

    /**
     * Related ads account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    /**
     * Related ads campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Related ads campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adset()
    {
        return $this->belongsTo(AdSet::class, 'adset_id');
    }

    /**
     * Related ads campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id');
    }
    /**
     * Related offer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Scope campaigns to visible
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->check() && auth()->user()->isVerifier()) {
            $builder->where('user_id', auth()->id());
        }

        return $builder;

        if (auth()->check() && auth()->user()->isBuyer()) {
            $builder->whereIn(
                'account_id',
                auth()->user()->accounts()->pluck('account_id')->values()
            );
        }
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        if (
            auth()->user() instanceof User &&
            !auth()->user()->isAdmin() && !auth()->user()->isSupport()
        ) {
            $builder->whereIn(
                static::getTable() . '.offer_id',
                auth()->user()->allowedOffers->pluck('id')->values()
            );
        }

        return $builder;
    }
}
