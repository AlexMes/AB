<?php

namespace App\Facebook;

use App\AdsBoard;
use App\AgeInsights;
use App\Facebook\Events\Profiles\Created;
use App\Facebook\Events\Profiles\Deleting;
use App\Facebook\Events\Profiles\Updated;
use App\Facebook\Events\Profiles\Updating;
use App\Facebook\Jobs\CollectAccounts;
use App\Facebook\Jobs\CollectAds;
use App\Facebook\Jobs\CollectAdSets;
use App\Facebook\Jobs\CollectCampaigns;
use App\Facebook\Jobs\CollectProfilePages;
use App\Facebook\Jobs\RefreshProfileInformation;
use App\Facebook\Jobs\SyncAccountGroup;
use App\Facebook\Traits\HasIssues;
use App\Group;
use App\Http\Controllers\Profiles\Ads;
use App\Insights;
use App\Issue;
use App\PlatformInsights;
use App\Traits\HasVisibilityScope;
use App\User;
use Facebook\Facebook;
use FacebookAds\Api;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Facebook\Profile
 *
 * @property int $id
 * @property string $name
 * @property string $fbId
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $issue_registered_at
 * @property int|null $user_id
 * @property string|null $last_synced_at
 * @property int|null $group_id
 * @property string|null $app_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Account[] $accounts
 * @property-read int|null $accounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Ad[] $ads
 * @property-read int|null $ads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\AdSet[] $adsets
 * @property-read int|null $adsets_count
 * @property-read \App\Facebook\FacebookApp|null $app
 * @property-read \Illuminate\Database\Eloquent\Collection|AgeInsights[] $cachedAgeInsights
 * @property-read int|null $cached_age_insights_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Insights[] $cachedInsights
 * @property-read int|null $cached_insights_count
 * @property-read \Illuminate\Database\Eloquent\Collection|PlatformInsights[] $cachedPlatformInsights
 * @property-read int|null $cached_platform_insights_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Campaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read bool $has_issues
 * @property-read string|null $last_issue
 * @property-read Group|null $group
 * @property-read \Illuminate\Database\Eloquent\Collection|Issue[] $issues
 * @property-read int|null $issues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\ProfilePage[] $pages
 * @property-read int|null $pages_count
 * @property-read User|null $user
 *
 * @method static Builder|Profile commentators()
 * @method static Builder|Profile issueDoesntExist()
 * @method static Builder|Profile newModelQuery()
 * @method static Builder|Profile newQuery()
 * @method static Builder|Profile query()
 * @method static Builder|Profile visible()
 * @method static Builder|Profile whereAppId($value)
 * @method static Builder|Profile whereCreatedAt($value)
 * @method static Builder|Profile whereFbId($value)
 * @method static Builder|Profile whereGroupId($value)
 * @method static Builder|Profile whereId($value)
 * @method static Builder|Profile whereIssueRegisteredAt($value)
 * @method static Builder|Profile whereLastSyncedAt($value)
 * @method static Builder|Profile whereName($value)
 * @method static Builder|Profile whereToken($value)
 * @method static Builder|Profile whereUpdatedAt($value)
 * @method static Builder|Profile whereUserId($value)
 * @method static Builder|Profile withIssuesCount()
 * @method static Builder|Profile withLastIssue()
 * @method static Builder|Profile withoutIssues()
 * @mixin \Eloquent
 */
class Profile extends Model
{
    use HasIssues;
    use HasVisibilityScope;

    public const DISABLED_CHECKPOINT = 'You cannot access the app till you log in to www.facebook.com and follow the instructions given.';
    public const DISABLED_PASSWORD   = 'Error validating access token: The session has been invalidated because the user changed their password or Facebook has changed the session for security reasons.';

    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_profiles';

    /**
     * Guard attributes from mass-assignment
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Hide attributes from JSON
     *
     * @var array
     */
    protected $hidden = ['token'];

    /**
     * Map model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created'   => Created::class,
        'updated'   => Updated::class,
        'updating'  => Updating::class,
        'deleting'  => Deleting::class
    ];

    /**
     * Attach attributes on every request
     *
     * @var array
     */
    protected $appends = [
        'has_issues',
    ];

    /**
     * Find a facebook account by it's ID
     *
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model | null
     */
    public static function findById(string $id)
    {
        return static::where('fbId', $id)->first();
    }

    /**
     * Scope profiles to enabled only
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeIssueDoesntExist(Builder $query)
    {
        $query->whereNotExists(function ($q) {
            $q->from('issues')
                ->where('issuable_type', 'profiles')
                ->whereRaw('"facebook_profiles"."id" = "issues"."issuable_id"::INTEGER')
                ->whereNull('fixed_at');
        });
    }

    /**
     * All available ads accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function campaigns()
    {
        return $this->hasManyThrough(
            Campaign::class,
            Account::class,
            null,
            null,
            null,
            'account_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adsets()
    {
        return $this->hasManyThrough(
            AdSet::class,
            Account::class,
            null,
            null,
            null,
            'account_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ads()
    {
        return $this->hasManyThrough(
            Ad::class,
            Account::class,
            null,
            null,
            null,
            'account_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany(ProfilePage::class);
    }

    /**
     * Load fresh data from the Facebook API
     *
     * @param bool $force
     *
     * @return void
     */
    public function refreshFacebookData($force = false)
    {
        RefreshProfileInformation::withChain([
            new CollectAccounts($this, $force),
            new CollectCampaigns($this, $force),
            new CollectAdSets($this, $force),
            new CollectAds($this, $force),
            new CollectProfilePages($this, $force),
        ])->dispatch($this, $force)->allOnQueue(AdsBoard::QUEUE_FACEBOOK);
    }

    /**
     * Assigned user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope profiles to allowed only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeVisible($builder)
    {
        if (auth()->check() && auth()->user()->role == User::BUYER) {
            $builder->where('user_id', auth()->id())->orWhere(function (Builder $query) {
                return $query->where('user_id', null)->where('created_at', '>', now()->subMonth()->toDateTimeString());
            });
        }

        if (auth()->check() && auth()->user()->isVerifier()) {
            $builder->where('user_id', auth()->id());
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @param $newGroup
     * @param $oldGroup
     */
    public function syncGroup($newGroup, $oldGroup)
    {
        SyncAccountGroup::dispatch($this, $newGroup, $oldGroup);
    }

    /**
     * Limit query to those who must leave comments
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommentators(Builder $query)
    {
        return $query->where('sendComments', true);
    }

    /**
     * Determine when current profile is external
     *
     * @return bool
     */
    public function hasNotToken()
    {
        return is_null($this->token) || $this->token === 'NONE';
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithIssuesCount(Builder $builder)
    {
        return $builder->addSelect([
            'pending_issues' => Issue::selectRaw('count(*)')
                ->whereIssuableType('profiles')
                ->whereNull('fixed_at')
                ->whereColumn('facebook_profiles.id', '=', DB::raw('issues.issuable_id::int'))
        ]);
    }

    /**
     * Load latest issue message.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithLastIssue(Builder $builder)
    {
        return $builder->addSelect([
            'last_issue' => Issue::select('message')
                ->whereIssuableType('profiles')
                ->whereNull('fixed_at')
                ->latest()
                ->whereColumn('facebook_profiles.id', '=', DB::raw('issues.issuable_id::int'))
                ->limit(1)
        ]);
    }

    /**
     * @return \FacebookAds\Object\User
     */
    public function instance()
    {
        Api::init(config('facebook.app_id'), config('facebook.app_secret'), $this->token);

        return new \FacebookAds\Object\User($this->fbId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {
        return $this->belongsTo(FacebookApp::class, 'app_id', 'id');
    }

    /**
     * Init marketing api session for php-sdk
     *
     * @return \FacebookAds\Api|null
     */
    public function initMarketingApi()
    {
        return FacebookApp::initApi($this->app_id, $this->token);
    }

    /**
     * Build Facebook http client
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return \Facebook\Facebook
     */
    public function getFacebookClient()
    {
        return FacebookApp::build($this->app_id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function cachedInsights()
    {
        return $this->hasManyThrough(
            Insights::class,
            Account::class,
            null,
            null,
            null,
            'account_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function cachedAgeInsights()
    {
        return $this->hasManyThrough(
            AgeInsights::class,
            Account::class,
            null,
            null,
            null,
            'account_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function cachedPlatformInsights()
    {
        return $this->hasManyThrough(
            PlatformInsights::class,
            Account::class,
            null,
            null,
            null,
            'account_id'
        );
    }

    /**
     * Deletes profile and all its data such insights, accounts...
     *
     * @throws \Throwable
     */
    public function remove()
    {
        DB::transaction(function () {
            $this->cachedInsights()->delete();
            $this->cachedAgeInsights()->delete();
            $this->cachedPlatformInsights()->delete();

            $this->ads()->delete();
            $this->adsets()->delete();
            $this->campaigns()->delete();
            $this->accounts()->delete();

            $this->delete();
        });
    }
}
