<?php

namespace App\Facebook;

use App\Designer;
use App\Domain;
use App\Facebook\Events\Ads\Created;
use App\Facebook\Events\Ads\Saved;
use App\Facebook\Events\Ads\Updated;
use App\Facebook\Traits\HasProfile;
use App\Insights;
use App\Tag;
use App\Traits\SaveQuietly;
use FacebookAds\Api;
use FacebookAds\Object\Ad as ObjectAd;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Values\AdEffectiveStatusValues;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * App\Facebook\Ad
 *
 * @property string $id
 * @property string $name
 * @property string $account_id
 * @property string $campaign_id
 * @property string $adset_id
 * @property string|null $status
 * @property string|null $effective_status
 * @property string|null $configured_status
 * @property array|null $ad_review_feedback
 * @property array|null $targeting
 * @property array|null $recommendations
 * @property array|null $issues_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $creative
 * @property string|null $page_id
 * @property string|null $creative_url
 * @property int|null $domain_id
 * @property string|null $reject_reason
 * @property int|null $designer_id
 * @property-read \App\Facebook\Account $account
 * @property-read \App\Facebook\AdSet $adset
 * @property-read \Illuminate\Database\Eloquent\Collection|Insights[] $cachedInsights
 * @property-read int|null $cached_insights_count
 * @property-read Designer|null $designer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\AdDisapproval[] $disapprovals
 * @property-read int|null $disapprovals_count
 * @property-read Domain|null $domain
 * @property-read \App\Facebook\ProfilePage|null $page
 * @property-read \App\Facebook\Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read int|null $tags_count
 *
 * @method static Builder|Ad active()
 * @method static Builder|Ad approved()
 * @method static Builder|Ad newModelQuery()
 * @method static Builder|Ad newQuery()
 * @method static Builder|Ad query()
 * @method static Builder|Ad rejected()
 * @method static Builder|Ad visible()
 * @method static Builder|Ad whereAccountId($value)
 * @method static Builder|Ad whereAdReviewFeedback($value)
 * @method static Builder|Ad whereAdsetId($value)
 * @method static Builder|Ad whereCampaignId($value)
 * @method static Builder|Ad whereConfiguredStatus($value)
 * @method static Builder|Ad whereCreatedAt($value)
 * @method static Builder|Ad whereCreative($value)
 * @method static Builder|Ad whereCreativeUrl($value)
 * @method static Builder|Ad whereDesignerId($value)
 * @method static Builder|Ad whereDomainId($value)
 * @method static Builder|Ad whereEffectiveStatus($value)
 * @method static Builder|Ad whereId($value)
 * @method static Builder|Ad whereIssuesInfo($value)
 * @method static Builder|Ad whereName($value)
 * @method static Builder|Ad wherePageId($value)
 * @method static Builder|Ad whereRecommendations($value)
 * @method static Builder|Ad whereRejectReason($value)
 * @method static Builder|Ad whereStatus($value)
 * @method static Builder|Ad whereTargeting($value)
 * @method static Builder|Ad whereUpdatedAt($value)
 * @method static Builder|Ad withCurrentCpl()
 * @method static Builder|Ad withCurrentSpend()
 * @mixin \Eloquent
 */
class Ad extends Model
{
    use HasProfile,
        SaveQuietly;

    /** @var array  */
    public const FB_FIELDS = [
        AdFields::ID,
        AdFields::NAME,
        AdFields::ACCOUNT_ID,
        AdFields::CAMPAIGN_ID,
        AdFields::ADSET_ID,
        AdFields::STATUS,
        AdFields::EFFECTIVE_STATUS,
        AdFields::CONFIGURED_STATUS,
        AdFields::AD_REVIEW_FEEDBACK,
        AdFields::TARGETING,
        AdFields::RECOMMENDATIONS,
        AdFields::ISSUES_INFO,
        'creative.fields(effective_object_story_id, object_story_spec)'
    ];

    public const APPROVED_STATUSES = [
        AdEffectiveStatusValues::ACTIVE,
        AdEffectiveStatusValues::PAUSED,
        AdEffectiveStatusValues::ADSET_PAUSED,
        AdEffectiveStatusValues::CAMPAIGN_PAUSED,
    ];

    public const REJECTED_STATUSES = [
        AdEffectiveStatusValues::WITH_ISSUES,
        AdEffectiveStatusValues::DISAPPROVED,
    ];

    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'facebook_ads';

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
     * Model attributes casting
     *
     * @var array
     */
    protected $casts = [
        'ad_review_feedback' => 'json',
        'targeting'          => 'json',
        'recommendations'    => 'json',
        'issues_info'        => 'json',
        'creative'           => 'json'
    ];

    /**
     * Bind model attributes
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created'  => Created::class,
        'saved'    => Saved::class,
        'updating' => Updated::class,
    ];

    /**
     * Parent adset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adset()
    {
        return $this->belongsTo(AdSet::class, 'adset_id');
    }

    /**
     * Get all active ads
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->whereNotIn(
            'effective_status',
            ['WITH_ISSUES', 'PENDING_REVIEW', 'DISAPPROVED','ARCHIVED','DELETED','PENDING_REVIEW']
        );
    }

    /**
     * Fetch the comments
     *
     * @return \Illuminate\Support\Collection
     */
    public function comments()
    {
        if ($this->hasPostId()) {
            try {
                return collect($this->account
                    ->getFacebookClient()
                    ->get(
                        sprintf("%s/comments", $this->creative['effective_object_story_id']),
                        $this->getToken()
                    )->getDecodedBody()['data']);
            } catch (\Throwable $exception) {
                Log::warning('Unable to load comments.');
                Log::warning($exception->getMessage());
            }
        }

        return collect();
    }

    /**
     * Related ad account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    /**
     * Determine if we have post id
     *
     * @return bool
     */
    public function hasPostId()
    {
        if (is_array($this->creative)) {
            return array_key_exists('effective_object_story_id', $this->creative);
        }

        return false;
    }

    /**
     * Get effective post
     *
     * @return mixed|null
     */
    public function getPostId()
    {
        if ($this->hasPostId()) {
            return $this->creative['effective_object_story_id'];
        }

        return null;
    }

    /**
     * Get facebook access token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->account->getToken();
    }

    /**
     * Get page level token
     *
     * @return mixed
     */
    public function pageToken()
    {
        return optional($this->page)->access_token;
    }

    /**
     * Get related page id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(ProfilePage::class, 'page_id');
    }

    /**
     * Parse page id from creative story ID
     *
     * @return bool|string
     */
    public function getPageId()
    {
        if ($this->hasPostId()) {
            return explode('_', $this->creative['effective_object_story_id'])[0];
        }

        return null;
    }

    /**
     * Cached statistics for ad
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cachedInsights()
    {
        return $this->hasMany(Insights::class);
    }

    /**
     * Tags attached to the ad
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'facebook_ad_tag');
    }

    /**
     * Gets creative_url from creative
     *
     * @param array $creative
     *
     * @return string|null
     */
    public static function getCreativeUrl($creative)
    {
        if (empty($creative['object_story_spec'])) {
            return null;
        }
        $objectStorySpec = $creative['object_story_spec'];

        if (isset($objectStorySpec['video_data'])) {
            return $objectStorySpec['video_data']['call_to_action']['value']['link'] ?? null;
        } elseif (isset($objectStorySpec['link_data']) || isset($objectStorySpec['template_data'])) {
            $data = $objectStorySpec['link_data'] ?? $objectStorySpec['template_data'];
            if (!empty($data['link'])) {
                return $data['link'];
            }

            return $data['call_to_action']['value']['link'] ?? null;
        }

        return null;
    }

    /**
     * Set creative and promoted link url
     *
     * @param $creative
     *
     * @return void
     */
    public function setCreativeAttribute($creative)
    {
        $this->attributes['creative']     = json_encode($creative);
        $this->attributes['creative_url'] = self::getCreativeUrl($creative);
    }

    /**
     * Set ad review feedback with shortcut for reason
     *
     * @param array|null $value
     *
     * @return void
     */
    public function setAdReviewFeedbackAttribute($value)
    {
        $this->attributes['ad_review_feedback'] = json_encode($value);
        $this->attributes['reject_reason']      = $this->extractRejectReason($value);
    }

    /**
     * Extracts reject reason from Facebook crazy array
     *
     * @param array|null $feedback
     *
     * @return void
     */
    public function extractRejectReason($feedback)
    {
        if (! is_array($feedback)) {
            return null;
        }
        if (array_key_exists('global', $feedback)) {
            return array_keys($feedback['global'])[0] ?? null;
        }

        return null;
    }

    /**
     * Domain where this ad is attached
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Determine is current ad has domain attached
     *
     * @return bool
     */
    public function hasDomain()
    {
        return $this->domain_id !== null;
    }

    /**
     * Disapproved status log for the ad
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disapprovals()
    {
        return $this->hasMany(AdDisapproval::class);
    }

    /**
     * Scope for approved ads
     *
     * @param Builder $builder
     *
     * @return void
     */
    public function scopeApproved(Builder $builder)
    {
        return $builder->whereIn('effective_status', ['ACTIVE', 'PAUSED', 'ADSET_PAUSED', 'CAMPAIGN_PAUSED']);
    }

    /**
     * Scope to rejected ads
     *
     * @param Builder $builder
     *
     * @return void
     */
    public function scopeRejected(Builder $builder)
    {
        return $builder->whereIn('effective_status', ['WITH_ISSUES','DISAPPROVED']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }

    /**
     * Spawn instance to call facebook api
     *
     * @return ObjectAd
     */
    public function instance()
    {
        $this->initMarketingApi();

        return new ObjectAd($this->id);
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
                ->whereColumn('facebook_ads.id', '=', 'facebook_cached_insights.ad_id'),
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
                ->whereColumn('facebook_ads.id', '=', 'facebook_cached_insights.ad_id'),
        ]);
    }

    /**
     * Resolve, initialize and connect to FB marketing api
     *
     * @return Api|null
     */
    public function initMarketingApi()
    {
        return $this->account->initMarketingApi();
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
        return $this->account->getFacebookClient();
    }

    /**
     * Scope ads to visible
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
    }
}
