<?php

namespace App\Facebook;

use App\AdsBoard;
use App\Facebook\Contracts\Insightful;
use App\Facebook\Events\Accounts\Created;
use App\Facebook\Events\Accounts\Updated;
use App\Facebook\Events\Accounts\Updating;
use App\Facebook\Jobs\CacheAgeInsights;
use App\Facebook\Jobs\CacheInsights;
use App\Facebook\Jobs\CachePlatformInsights;
use App\Facebook\Objects\Insight;
use App\Group;
use App\Insights;
use App\User;
use Arr;
use FacebookAds\Object\AdAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Facebook\Account
 *
 * @property string $id
 * @property string $account_id
 * @property string $name
 * @property string $age
 * @property string $account_status
 * @property string $amount_spent
 * @property string $balance
 * @property string $currency
 * @property int $profile_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $disable_reason
 * @property int|null $group_id
 * @property string|null $comment
 * @property string|null $banned_at
 * @property string|null $card_number
 * @property string|null $ad_disabled_at
 * @property bool $stopper_enabled
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Ad[] $ads
 * @property-read int|null $ads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\AdSet[] $adsets
 * @property-read int|null $adsets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Insights[] $cachedInsights
 * @property-read int|null $cached_insights_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Campaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read string $expenses
 * @property-read string $status
 * @property-read Group|null $group
 * @property-read \App\Facebook\Profile $profile
 * @property-write mixed $funding_source_details
 * @property-read User|null $user
 *
 * @method static Builder|Account active()
 * @method static Builder|Account banned()
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account query()
 * @method static Builder|Account visible()
 * @method static Builder|Account whereAccountId($value)
 * @method static Builder|Account whereAccountStatus($value)
 * @method static Builder|Account whereAdDisabledAt($value)
 * @method static Builder|Account whereAge($value)
 * @method static Builder|Account whereAmountSpent($value)
 * @method static Builder|Account whereBalance($value)
 * @method static Builder|Account whereBannedAt($value)
 * @method static Builder|Account whereCardNumber($value)
 * @method static Builder|Account whereComment($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereCurrency($value)
 * @method static Builder|Account whereDisableReason($value)
 * @method static Builder|Account whereGroupId($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereName($value)
 * @method static Builder|Account whereProfileId($value)
 * @method static Builder|Account whereStopperEnabled($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @method static Builder|Account withCurrentCpl()
 * @method static Builder|Account withCurrentSpend()
 * @method static Builder|Account withLifetime()
 * @method static Builder|Account withSpendForPeriod($since = null, $until = null)
 * @method static Builder|Account withTotalSpend()
 * @mixin \Eloquent
 */
class Account extends Model implements Insightful
{
    public const FB_FIELDS = [
        'id',
        'account_id',
        'account_status',
        'age',
        'amount_spent',
        'balance',
        'name',
        'currency',
        'disable_reason',
        'funding_source_details'
    ];

    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_ads_accounts';

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
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created'  => Created::class,
        'updated'  => Updated::class,
        'updating' => Updating::class,
    ];

    /**
     * @var array
     */
    protected $appends = ['status', 'expenses'];

    /**
     * @var string[]
     */
    protected $casts = [
        'stopper_enabled' => 'bool',
    ];

    /**
     * Related Facebook profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * All related campaigns
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'account_id', 'account_id');
    }

    /**
     * Related adsets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adsets()
    {
        return $this->hasMany(AdSet::class, 'account_id', 'account_id');
    }

    /**
     * Related ads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    /**
     * Get account adsets
     *
     * @return \FacebookAds\ApiRequest|\FacebookAds\Cursor|\FacebookAds\Http\ResponseInterface|null
     */
    public function getAdSets()
    {
        return $this->instance()->getAdSets();
    }

    /**
     * Get account access token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->resolveFBAccount()->token;
    }

    /**
     * @param array $fields
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     */
    public function insights(array $fields = [], array $params = [])
    {
        return collect($this->instance()
            ->getInsights($fields, $params))
            ->mapInto(Insight::class);
    }

    /**
     * @return \FacebookAds\Object\AdAccount
     */
    public function instance()
    {
        $this->initMarketingApi();

        return new AdAccount($this->id);
    }

    /**
     * Map status codes to more friendly presentation
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return [
            1   => 'ACTIVE',
            2   => 'DISABLED',
            3   => 'UNSETTLED',
            7   => 'PENDING_RISK_REVIEW',
            8   => 'PENDING_SETTLEMENT',
            9   => 'IN_GRACE_PERIOD',
            100 => 'PENDING_CLOSURE',
            101 => 'CLOSED',
            201 => 'ANY_ACTIVE',
            202 => 'ANY_CLOSED',
        ][$this->account_status];
    }

    /**
     * Format ad account age
     *
     * @param float|string $age
     *
     * @return string
     */
    public function getAgeAttribute($age)
    {
        return number_format($age, 0);
    }

    /**
     * @return string
     */
    public function getExpensesAttribute()
    {
        return sprintf(
            '%s %s',
            number_format($this->amount_spent / 100, 2, '.', ','),
            $this->currency
        );
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function getBalanceAttribute($value)
    {
        return sprintf(
            '%s %s',
            number_format($value / 100, 2, '.', ','),
            $this->currency
        );
    }

    /**
     * Get entity id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get leads count
     *
     * @return int
     */
    public function getLeadsCount(): int
    {
        return 0;
    }

    /**
     * Get effective status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get related Facebook account
     *
     * @return \App\Facebook\Profile
     */
    public function getFBAccount(): Profile
    {
        return $this->profile;
    }

    /**
     * Get daily budget
     *
     * @return string
     */
    public function getBudget(): string
    {
        return $this->amount_spent;
    }

    /**
     * Map disabled reason to readable value
     *
     * @param int $value
     */
    public function setDisableReasonAttribute($value)
    {
        $this->attributes['disable_reason'] = [
            0 => 'NONE',
            1 => 'ADS_INTEGRITY_POLICY',
            2 => 'ADS_IP_REVIEW',
            3 => 'RISK_PAYMENT',
            4 => 'GRAY_ACCOUNT_SHUT_DOWN',
            5 => 'ADS_AFC_REVIEW',
            6 => 'BUSINESS_INTEGRITY_RAR',
            7 => 'PERMANENT_CLOSE',
            8 => 'UNUSED_RESELLER_ACCOUNT',
            9 => 'UNUSED_ACCOUNT',
        ][$value];
    }

    /**
     * Scope visible accounts for buyers
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->check() && auth()->user()->isVerifier()) {
            $builder->whereHas('profile', fn ($q) => $q->where('user_id', auth()->id()));
        }

        if (auth()->check() && auth()->user()->role == User::BUYER) {
            $builder->whereIn('profile_id', auth()->user()->profiles()->visible()->pluck('id')->values());
        }

        return $builder;
    }

    /**
     * Get account for specific user(s)
     *
     * @param mixed $users
     *
     * @return \Illuminate\Support\Collection
     */
    public static function forUsers($users)
    {
        return User::whereIn('id', Arr::wrap($users))->get()
            ->map(function (User $user) {
                return $user->accounts;
            })->flatten(1);
    }

    /**
     * Cache account insights for a specific date
     *
     * @param $date
     */
    public function cacheInsights($date)
    {
        CacheInsights::dispatch($this, $date)->onQueue(AdsBoard::QUEUE_INSIGHTS);
    }

    /**
     * Cache account insights for a specific date and breakdown "age"
     *
     * @param $date
     */
    public function cacheAgeInsights($date)
    {
        CacheAgeInsights::dispatch($this, $date)->onQueue(AdsBoard::QUEUE_INSIGHTS);
    }

    /**
     * Cache account insights for a specific date and breakdowns
     * device_platform, publisher_platform, impression_device
     * and platform_position
     *
     * @param $date
     */
    public function cachePlatformInsights($date)
    {
        CachePlatformInsights::dispatch($this, $date)->onQueue(AdsBoard::QUEUE_INSIGHTS);
    }
    /**
     * Related Facebook comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'account_id');
    }

    /**
     * Get only active adsets
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->where('effective_status', 'ACTIVE');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get only banned
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBanned(Builder $builder)
    {
        return $builder->whereNotNull('banned_at');
    }

    /**
     * Determines is account has spend for current date
     *
     * @return bool
     */
    public function hasSpendToday()
    {
        return $this->cachedInsights()->whereDate('date', now())->exists();
    }

    /**
     * Get account lifetime from creation to ban in hours
     *
     * @return int
     */
    protected function getLifetimeAttribute()
    {
        if (isset($this->attributes['lifetime'])) {
            return $this->attributes['lifetime'];
        }

        try {
            $start = Carbon::parse($this->created_at);
            $end   = Carbon::parse($this->banned_at);

            return $start->diffInHours($end);
        } catch (\Throwable $exception) {
            return -1;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Profile::class,
            'id',
            'id',
            'profile_id',
            'user_id'
        );
    }

    /**
     * @param array $value
     */
    public function setFundingSourceDetailsAttribute($value)
    {
        if (is_array($value) && isset($value['type']) && isset($value['display_string'])) {
            // Type of the funding source
            if ($value['type'] == 1) {
                $this->attributes['card_number'] = digits($value['display_string']);
            }
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentSpend(Builder $builder)
    {
        return $builder->addSelect([
            'spend' => Insights::selectRaw('sum(spend::decimal)')
                ->whereDate('date', now())
                ->whereColumn('facebook_ads_accounts.account_id', '=', 'facebook_cached_insights.account_id'),
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCpl(Builder $builder)
    {
        return $builder->addSelect([
            'cpl' => Insights::selectRaw('round(sum(spend::decimal) / nullif(sum(leads_cnt),0), 2)')
                ->whereDate('date', now())
                ->whereColumn('facebook_ads_accounts.account_id', '=', 'facebook_cached_insights.account_id'),
        ]);
    }

    /**
     * Account lifetime
     *
     * @param Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithLifetime(Builder $builder)
    {
        return $builder->addSelect(DB::raw('EXTRACT(EPOCH FROM COALESCE(facebook_ads_accounts.banned_at, now()) - facebook_ads_accounts.created_at)::int as lifetime'));
    }

    /**
     * Spend for specific period
     *
     * @param Builder     $builder
     * @param Carbon|null $since
     * @param Carbon|null $until
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSpendForPeriod(Builder $builder, $since = null, $until = null)
    {
        return $builder->addSelect([
            'spend' => Insights::selectRaw('sum(spend::decimal)')
                ->when(
                    $since,
                    fn ($query) => $query->whereDate('date', '>=', $since),
                    fn ($query) => $query->whereDate('date', '>=', now())
                )
                ->when(
                    $until,
                    fn ($query) => $query->whereDate('date', '<=', $until),
                    fn ($query) => $query->whereDate('date', '<=', now())
                )
                ->whereColumn('facebook_ads_accounts.account_id', '=', 'facebook_cached_insights.account_id'),
        ]);
    }

    /**
     * Total spend for account
     *
     * @param Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTotalSpend(Builder $builder)
    {
        return $builder->addSelect([
            'spend' => Insights::selectRaw('sum(spend::decimal)')
                ->whereColumn('facebook_ads_accounts.account_id', '=', 'facebook_cached_insights.account_id'),
        ]);
    }

    /**
     * Cached insights for account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cachedInsights()
    {
        return $this->hasMany(Insights::class, 'account_id', 'account_id');
    }

    /**
     * @return Profile
     */
    public function resolveFBAccount(): Profile
    {
        // If profile got checkpoint or something, try to find other token
        if ($this->profile->hasIssues()) {
            /** @var \App\Facebook\Account $reserve */
            $reserve = self::query()
                ->where('account_id', $this->account_id)
                ->where('profile_id', '!=', $this->profile_id)
                ->get()
                ->reject(function (Account $account) {
                    return $account->profile->hasIssues();
                })->first();

            if ($reserve) {
                return $reserve->profile;
            }
        }

        return $this->profile;
    }

    /**
     * @return \FacebookAds\Api|null
     */
    public function initMarketingApi()
    {
        return $this->resolveFBAccount()->initMarketingApi();
    }

    /**
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return \Facebook\Facebook
     */
    public function getFacebookClient()
    {
        return $this->resolveFBAccount()->getFacebookClient();
    }
}
