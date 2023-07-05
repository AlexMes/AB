<?php

namespace App\Facebook;

use App\Facebook\Contracts\Insightful;
use App\Facebook\Events\Campaigns\CampaignCreated;
use App\Facebook\Events\Campaigns\CampaignSaving;
use App\Facebook\Events\Campaigns\CampaignUpdated;
use App\Facebook\Traits\FormatsFacebookTimestamps;
use App\Facebook\Traits\HasBudgets;
use App\Insights;
use App\Jobs\DetectCampaignOffer;
use App\Offer;
use App\Traits\AppendAccessAttributes;
use Facebook\Exceptions\FacebookSDKException;
use FacebookAds\Api;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Object\Campaign as FacebookCampaign;
use FacebookAds\Object\Fields\CampaignFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Facebook\Campaign
 *
 * @property string $id
 * @property string $name
 * @property string $account_id
 * @property string $daily_budget
 * @property string $budget_remaining
 * @property mixed $lifetime_budget
 * @property string|null $buying_type
 * @property string|null $status
 * @property string|null $effective_status
 * @property string|null $configured_status
 * @property array|null $issues_info
 * @property array|null $recommendations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $offer_id
 * @property string|null $utm_key
 * @property-read \App\Facebook\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\AdSet[] $adSets
 * @property-read int|null $ad_sets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Ad[] $ads
 * @property-read int|null $ads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Insights[] $cachedInsights
 * @property-read int|null $cached_insights_count
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 * @property-read Offer|null $offer
 * @property-write mixed $created_time
 * @property-write mixed $start_time
 * @property-write mixed $time_start
 * @property-write mixed $time_stop
 * @property-write mixed $updated_time
 *
 * @method static Builder|Campaign active()
 * @method static Builder|Campaign newModelQuery()
 * @method static Builder|Campaign newQuery()
 * @method static Builder|Campaign query()
 * @method static Builder|Campaign visible()
 * @method static Builder|Campaign whereAccountId($value)
 * @method static Builder|Campaign whereBudgetRemaining($value)
 * @method static Builder|Campaign whereBuyingType($value)
 * @method static Builder|Campaign whereConfiguredStatus($value)
 * @method static Builder|Campaign whereCreatedAt($value)
 * @method static Builder|Campaign whereDailyBudget($value)
 * @method static Builder|Campaign whereEffectiveStatus($value)
 * @method static Builder|Campaign whereId($value)
 * @method static Builder|Campaign whereIssuesInfo($value)
 * @method static Builder|Campaign whereLifetimeBudget($value)
 * @method static Builder|Campaign whereName($value)
 * @method static Builder|Campaign whereOfferId($value)
 * @method static Builder|Campaign whereRecommendations($value)
 * @method static Builder|Campaign whereStatus($value)
 * @method static Builder|Campaign whereUpdatedAt($value)
 * @method static Builder|Campaign whereUtmKey($value)
 * @method static Builder|Campaign withCplForDate($date)
 * @method static Builder|Campaign withCurrentCpl()
 * @method static Builder|Campaign withCurrentSpend()
 * @method static Builder|Campaign withSpendForDate($date)
 * @mixin \Eloquent
 */
class Campaign extends Model implements Insightful
{
    use HasBudgets;
    use FormatsFacebookTimestamps;
    use AppendAccessAttributes;

    /** @var array  */
    public const FB_FIELDS = [
        CampaignFields::ID,
        CampaignFields::NAME,
        CampaignFields::ACCOUNT_ID,
        CampaignFields::DAILY_BUDGET,
        CampaignFields::BUDGET_REMAINING,
        CampaignFields::LIFETIME_BUDGET,
        CampaignFields::BUYING_TYPE,
        CampaignFields::EFFECTIVE_STATUS,
        CampaignFields::CONFIGURED_STATUS,
        CampaignFields::ISSUES_INFO,
        CampaignFields::RECOMMENDATIONS,
    ];

    /**
     * Table name in database
     *
     * @var string
     */
    protected $table   = 'facebook_campaigns';

    /**
     * Attributes protected from mass-assignment
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Disable incrementing primary key
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Cast attributes to native types
     *
     * @var array
     */
    protected $casts = [
        'issues_info'     => 'json',
        'recommendations' => 'json',
    ];

    /**
     * Map model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => CampaignCreated::class,
        'saving'  => CampaignSaving::class,
        'updated' => CampaignUpdated::class,
    ];

    /**
     * Append default attributes
     *
     * @var array
     */
    protected $appends = [
        'can_update'
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
     * All of related adsets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adSets()
    {
        return $this->hasMany(AdSet::class, 'campaign_id');
    }

    /**
     * Get account access token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->account->getToken();
    }

    /**
     * Collect campaign insights from API
     *
     * @param array $fields
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     */
    public function insights(array $fields = [], array $params = [])
    {
        return collect($this->instance()->getInsights(Insightful::FIELDS, $params))
            ->mapInto(\App\Facebook\Objects\Insight::class);
    }

    /**
     * Get Facebook SDK instance ready to go
     *
     * @return \FacebookAds\Object\Campaign
     */
    public function instance()
    {
        $this->initMarketingApi();

        return new FacebookCampaign($this->id);
    }

    /**
     * Start campaign
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function start()
    {
        try {
            $this->update(
                $this->instance()->updateSelf(static::FB_FIELDS, ['status' => 'ACTIVE'])->exportAllData()
            );
        } catch (FacebookSDKException | AuthorizationException $exception) {
            return response(['message' => $exception->getMessage()], 400);
        }


        return response()->json($this->fresh('account'), 202);
    }

    /**
     * Stop running campaign
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function stop()
    {
        try {
            $this->update(
                $this->instance()->updateSelf(static::FB_FIELDS, ['status' => 'PAUSED'])->exportAllData()
            );
        } catch (FacebookSDKException | AuthorizationException $exception) {
            return response(['message' => $exception->getMessage()], 400);
        }


        return response()->json($this->fresh('account'), 202);
    }

    /**
     * Update campaign budget
     *
     * @param mixed $budget
     * @param mixed $field
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateBudget($budget, $field)
    {
        try {
            $this->instance()->updateSelf(static::FB_FIELDS, [$field => $budget]);
        } catch (FacebookSDKException | AuthorizationException $exception) {
            return response(['message' => $exception->getMessage()], 400);
        }

        return response(null, 202);
    }

    /**
     * Run offer detection
     *
     * @return void
     */
    public function detectOffer()
    {
        DetectCampaignOffer::dispatch($this);
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
            $builder->whereHas('account.profile', fn ($q) => $q->where('user_id', auth()->id()));
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
     * sets what will further be called utm_campaign
     *
     * @return string
     */
    public function updateUtm()
    {
        $this->update([
            'utm_key' => trim(Str::afterLast($this->name, 'campaign-')) ?? $this->name,
        ]);
    }

    /**
     * Filter active only campaigns
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeActive(Builder $builder)
    {
        $builder->where('effective_status', 'ACTIVE');
    }

    /**
     * Load campaign with spend for specific date
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed                                 $date
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSpendForDate(Builder $builder, $date)
    {
        return $builder->addSelect([
            'spend' => Insights::selectRaw('sum(spend::decimal)')
                ->whereDate('date', $date)
                ->whereColumn('campaign_id', '=', 'facebook_campaigns.id'),
        ]);
    }

    /**
     * Load campaign with current spend
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentSpend(Builder $builder)
    {
        return $builder->withSpendForDate(now());
    }

    /**
     * Load campaign with CPL for a date
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed                                 $date
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCplForDate(Builder $builder, $date)
    {
        return $builder->addSelect([
            'cpl' => Insights::selectRaw('round(sum(spend::decimal) / nullif(sum(leads_cnt),0), 2)')
                ->whereDate('date', $date)
                ->whereColumn('campaign_id', '=', 'facebook_campaigns.id'),
        ]);
    }

    /**
     * Load campaign with current CPL
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCpl(Builder $builder)
    {
        return $builder->withCplForDate('now()');
    }

    /**
     * Gets related ads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    /**
     * Cached insights for campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cachedInsights()
    {
        return $this->hasMany(Insights::class, 'campaign_id');
    }

    /**
     * Resolve, initialize and connect to Facebook Marketing Api
     *
     * @return Api|null
     */
    public function initMarketingApi()
    {
        return $this->account->initMarketingApi();
    }
}
