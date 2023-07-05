<?php

namespace App\Facebook;

use App\Facebook\Contracts\Insightful;
use App\Facebook\Events\Adsets\Updated;
use App\Facebook\Traits\FormatsFacebookTimestamps;
use App\Facebook\Traits\HasBudgets;
use App\Insights;
use Facebook\Exceptions\FacebookSDKException;
use FacebookAds\Api;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Object\Fields\AdSetFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Facebook\AdSet
 *
 * @property string $id
 * @property string|null $name
 * @property string $account_id
 * @property string $campaign_id
 * @property string $budget_remaining
 * @property string $daily_budget
 * @property mixed $lifetime_budget
 * @property string|null $status
 * @property string|null $configured_status
 * @property string|null $effective_status
 * @property array|null $issues_info
 * @property array|null $targeting
 * @property string|null $destination_type
 * @property array|null $recommendations
 * @property string|null $billing_event
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $start_midnight
 * @property-read \App\Facebook\Account $account
 * @property-read \App\Facebook\Campaign $campaign
 * @property-write mixed $created_time
 * @property-write mixed $start_time
 * @property-write mixed $time_start
 * @property-write mixed $time_stop
 * @property-write mixed $updated_time
 *
 * @method static Builder|AdSet active()
 * @method static Builder|AdSet newModelQuery()
 * @method static Builder|AdSet newQuery()
 * @method static Builder|AdSet query()
 * @method static Builder|AdSet visible()
 * @method static Builder|AdSet whereAccountId($value)
 * @method static Builder|AdSet whereBillingEvent($value)
 * @method static Builder|AdSet whereBudgetRemaining($value)
 * @method static Builder|AdSet whereCampaignId($value)
 * @method static Builder|AdSet whereConfiguredStatus($value)
 * @method static Builder|AdSet whereCreatedAt($value)
 * @method static Builder|AdSet whereDailyBudget($value)
 * @method static Builder|AdSet whereDestinationType($value)
 * @method static Builder|AdSet whereEffectiveStatus($value)
 * @method static Builder|AdSet whereId($value)
 * @method static Builder|AdSet whereIssuesInfo($value)
 * @method static Builder|AdSet whereLifetimeBudget($value)
 * @method static Builder|AdSet whereName($value)
 * @method static Builder|AdSet whereRecommendations($value)
 * @method static Builder|AdSet whereStartMidnight($value)
 * @method static Builder|AdSet whereStatus($value)
 * @method static Builder|AdSet whereTargeting($value)
 * @method static Builder|AdSet whereUpdatedAt($value)
 * @method static Builder|AdSet withCplForDate($date)
 * @method static Builder|AdSet withCurrentCpl()
 * @method static Builder|AdSet withCurrentLeadsCount()
 * @method static Builder|AdSet withCurrentSpend()
 * @method static Builder|AdSet withLeadsCountForDate($date)
 * @method static Builder|AdSet withSpendForDate($date)
 * @mixin \Eloquent
 */
class AdSet extends Model implements Insightful
{
    use HasBudgets;
    use FormatsFacebookTimestamps;

    public const FB_FIELDS = [
        AdSetFields::ID,
        AdSetFields::NAME,
        AdSetFields::ACCOUNT_ID,
        AdSetFields::CAMPAIGN_ID,
        AdSetFields::BUDGET_REMAINING,
        AdSetFields::DAILY_BUDGET,
        AdSetFields::LIFETIME_BUDGET,
        AdSetFields::STATUS,
        AdSetFields::CONFIGURED_STATUS,
        AdSetFields::EFFECTIVE_STATUS,
        AdSetFields::ISSUES_INFO,
        AdSetFields::TARGETING,
        AdSetFields::DESTINATION_TYPE,
        AdSetFields::RECOMMENDATIONS,
        AdSetFields::BILLING_EVENT,
    ];

    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_adsets';

    /**
     * Guard model attributes
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
     * Attributes casting
     *
     * @var array
     */
    protected $casts = [
        'issues_info'     => 'json',
        'targeting'       => 'json',
        'recommendations' => 'json',
        'start_midnight'  => 'bool',
    ];

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'updated' => Updated::class
    ];

    /**
     * Related campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

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
     * Get account access token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->account->getToken();
    }

    /**
     * Collect related adset insights
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
     * Get Facebook API object instance
     *
     * @return \FacebookAds\Object\AdSet
     */
    public function instance()
    {
        $this->initMarketingApi();

        return new \FacebookAds\Object\AdSet($this->id);
    }

    /**
     * Start adset
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function start()
    {
        try {
            $this->update(
                $this->instance()->updateSelf(self::FB_FIELDS, ['status' => 'ACTIVE'])->exportAllData()
            );
        } catch (FacebookSDKException | AuthorizationException $exception) {
            return response(['message' => $exception->getMessage()], 400);
        }


        return response()->json($this->fresh('account'), 202);
    }

    /**
     * Stop adset
     *
     * @return \App\Facebook\AdSet|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function stop()
    {
        try {
            $this->update(
                $this->instance()->updateSelf(self::FB_FIELDS, ['status' => 'PAUSED'])->exportAllData()
            );
        } catch (FacebookSDKException | AuthorizationException $exception) {
            if (app()->runningInConsole()) {
                throw $exception;
            }

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
     * Scope adsets to only visible
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->check() && auth()->user()->isBuyer()) {
            $builder->whereIn('account_id', auth()->user()->accounts()->pluck('account_id')->values());
        }

        if (auth()->check() && auth()->user()->isVerifier()) {
            $builder->whereHas('account.profile', fn ($q) => $q->where('user_id', auth()->id()));
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeActive(Builder $builder)
    {
        $builder->where('effective_status', 'ACTIVE');
    }

    /**
     * Cut name of the adset
     *
     * @param string $name
     *
     * @return void
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = substr($name, 0, 250);
    }

    /**
     * Load adset with spend for specific date
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
                ->whereColumn('adset_id', '=', 'facebook_adsets.id'),
        ]);
    }

    /**
     * Load adset with current spend
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
     * Load adset with leads received for date
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed                                 $date
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithLeadsCountForDate(Builder $builder, $date)
    {
        return $builder->addSelect([
            'leads_count' => Insights::selectRaw('sum(leads_cnt)')
                ->whereDate('date', $date)
                ->whereColumn('adset_id', '=', 'facebook_adsets.id'),
        ]);
    }

    /**
     * Load adset with current leads count
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentLeadsCount(Builder $builder)
    {
        return $builder->withLeadsCountForDate(now());
    }

    /**
     * Load adset with CPL for a date
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
                ->whereColumn('adset_id', '=', 'facebook_adsets.id'),
        ]);
    }

    /**
     * Load adset with current CPL
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCpl(Builder $builder)
    {
        return $builder->withCplForDate(now());
    }

    /**
     * @return bool
     */
    public function fromAffiliateNetwork(): bool
    {
        return Str::contains($this->name, ['-an-']);
    }

    /**
     * @return bool
     */
    public function fromAll()
    {
        return Str::contains($this->name, '-all');
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
