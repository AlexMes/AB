<?php

namespace App;

use App\Events\Domains\Created;
use App\Events\Domains\Saved;
use App\Events\Domains\Saving;
use App\Events\Domains\Updated;
use App\Facebook\Account;
use App\Facebook\Ad;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Integrations\Form;
use App\Jobs\Domains\CheckAvailability;
use App\Notifications\Domain\Down;
use App\Notifications\Domain\Restored;
use App\Traits\AppendAccessAttributes;
use App\Traits\RecordEvents;
use App\Traits\SaveQuietly;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

/**
 * App\Domain
 *
 * @property int $id
 * @property mixed|null $effectiveDate
 * @property string|null $url
 * @property string $status
 * @property string $linkType
 * @property string|null $down_since
 * @property int|null $user_id
 * @property int|null $order_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $offer_id
 * @property string|null $host
 * @property string|null $ip
 * @property int|null $sp_id
 * @property int|null $bp_id
 * @property int|null $cloak_id
 * @property int|null $landing_id
 * @property string|null $reach_status
 * @property bool $splitterEnabled
 * @property string|null $failed_at
 * @property int $passed_ads_count
 * @property int $rejected_ads_count
 * @property int $total_ads
 * @property int|null $server_id
 * @property int|null $traffic_source_id
 * @property bool|null $allow_duplicates
 * @property-read \Illuminate\Database\Eloquent\Collection|Account[] $accounts
 * @property-read int|null $accounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Ad[] $ads
 * @property-read int|null $ads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|AdSet[] $adsets
 * @property-read int|null $adsets_count
 * @property-read \App\Page|null $bp
 * @property-read \Illuminate\Database\Eloquent\Collection|Campaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Form[] $forms
 * @property-read int|null $forms_count
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 * @property-read mixed $domains
 * @property-read Domain|null $land
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read \App\Offer|null $offer
 * @property-read \App\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SmsCampaign[] $smsCampaigns
 * @property-read int|null $sms_campaigns_count
 * @property-read \App\Page|null $sp
 * @property-read \App\TrafficSource|null $trafficSource
 * @property-read \App\User|null $user
 *
 * @method static Builder|Domain landing()
 * @method static Builder|Domain newModelQuery()
 * @method static Builder|Domain newQuery()
 * @method static Builder|Domain preLanding()
 * @method static Builder|Domain query()
 * @method static Builder|Domain ready()
 * @method static Builder|Domain service()
 * @method static Builder|Domain visible()
 * @method static Builder|Domain whereAllowDuplicates($value)
 * @method static Builder|Domain whereBpId($value)
 * @method static Builder|Domain whereCloakId($value)
 * @method static Builder|Domain whereCreatedAt($value)
 * @method static Builder|Domain whereDownSince($value)
 * @method static Builder|Domain whereEffectiveDate($value)
 * @method static Builder|Domain whereFailedAt($value)
 * @method static Builder|Domain whereHost($value)
 * @method static Builder|Domain whereId($value)
 * @method static Builder|Domain whereIp($value)
 * @method static Builder|Domain whereLandingId($value)
 * @method static Builder|Domain whereLinkType($value)
 * @method static Builder|Domain whereOfferId($value)
 * @method static Builder|Domain whereOrderId($value)
 * @method static Builder|Domain wherePassedAdsCount($value)
 * @method static Builder|Domain whereReachStatus($value)
 * @method static Builder|Domain whereRejectedAdsCount($value)
 * @method static Builder|Domain whereServerId($value)
 * @method static Builder|Domain whereSpId($value)
 * @method static Builder|Domain whereSplitterEnabled($value)
 * @method static Builder|Domain whereStatus($value)
 * @method static Builder|Domain whereTotalAds($value)
 * @method static Builder|Domain whereTrafficSourceId($value)
 * @method static Builder|Domain whereUpdatedAt($value)
 * @method static Builder|Domain whereUrl($value)
 * @method static Builder|Domain whereUserId($value)
 * @method static Builder|Domain withPassedCount()
 * @method static Builder|Domain withRejectedCount()
 * @mixin \Eloquent
 */
class Domain extends Model
{
    use AppendAccessAttributes;
    use SaveQuietly;
    use RecordEvents;

    /** @var string */
    public const IN_WORK   = 'development';
    /** @var string  */
    public const SCHEDULED = 'scheduled';
    /** @var string  */
    public const READY     = 'ready';
    /** @var string  */
    public const WORKED_OUT = 'worked_out';
    /** @var string  */
    public const LANDING   = 'landing';
    /** @var string  */
    public const PRELANDING = 'prelanding';
    /** @var string  */
    public const SERVICE    = 'service';
    /** @var array  */
    public const CLOAKS = ['imklo', 'fraudfilter', 'leadcloak'];
    /** @var string */
    public const PASSED   = 'passed';
    /** @var string */
    public const MISSED   = 'missed';
    /** @var string */
    public const BANNED   = 'banned';

    /** @var string */
    public const TRANSFERRED = 'transferred';

    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'domains';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Cast attributes to Carbon instances
     *
     * @var array
     */
    protected $casts = [
        'effectiveDate' => 'date:Y-m-d',
    ];

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Created::class,
        'updated' => Updated::class,
        'saved'   => Saved::class,
        'saving'  => Saving::class,
    ];

    /**
     * Append attributes
     *
     * @var array
     */
    protected $appends = [
        'can_update'
    ];

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
     * Related order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
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
     * Verify that domain is up and running
     *
     * @return void
     */
    public function check()
    {
        CheckAvailability::dispatch($this)->allOnQueue(AdsBoard::QUEUE_MONITORING);
    }

    /**
     * Scope for landings only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeLanding(Builder $builder)
    {
        $builder->where('linkType', self::LANDING);
    }

    /**
     * Scope for pre-landing pages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopePreLanding(Builder $builder)
    {
        $builder->where('linkType', self::PRELANDING);
    }

    /**
     * Scope for organization services
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeService(Builder $builder)
    {
        $builder->where('linkType', self::SERVICE);
    }

    /**
     * Mark website as up
     *
     * @return void
     */
    public function isUp()
    {
        if (! is_null($this->down_since)) {
            $recipients = [
                AdsBoard::devsChannel(),
                optional(optional($this->offer)->branch)->telegram_id
            ];

            Notification::sendNow($recipients, new Restored($this, Carbon::parse($this->down_since)));
            $this->update(['down_since' => null]);
        }
    }

    /**
     * Mark website as going down
     *
     * @return void
     */
    public function isDown()
    {
        // if down since is not empty, do nothing.
        if (is_null($this->down_since)) {
            // Confirm that domain is really fucking down
            CheckAvailability::dispatch($this, true)->delay(now()->addMinutes(2))->onQueue(AdsBoard::QUEUE_MONITORING);
        }
    }

    /**
     * determines ready state
     *
     * @return bool
     */
    public function isReady()
    {
        return $this->status === self::READY;
    }

    /**
     * Scope for ready domains
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeReady(Builder $builder)
    {
        $builder->where('status', self::READY);
    }

    /**
     * Chosen safe page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sp()
    {
        return $this->belongsTo(Page::class, 'sp_id');
    }

    /**
     * Chosen black page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bp()
    {
        return $this->belongsTo(Page::class, 'bp_id');
    }

    /**
     * Landing page related to domain
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function land()
    {
        return $this->belongsTo(Domain::class, 'landing_id');
    }

    /**
     * get domain Attribute
     *
     */
    public function getDomainsAttribute()
    {
        return [
            parse_url($this->url)['host'],
            'www.' . parse_url($this->url)['host']
        ];
    }

    /**
     * Limit domains to only visible
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('domains.created_at', '<', '2021-11-05');
        }
        if (optional(auth()->user())->isBuyer()) {
            return $builder->where('user_id', auth()->id());
        }
    }

    /**
     * Get all related sms campaigns
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smsCampaigns()
    {
        return $this->hasMany(SmsCampaign::class, 'landing_id');
    }

    /**
     * Get all related sms campaigns
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forms()
    {
        return $this->hasMany(Form::class, 'landing_id');
    }

    /**
     * Determine is offer detected
     *
     * @return bool
     */
    public function hasOffer()
    {
        return ! is_null($this->offer_id);
    }

    /**
     * Determine is splitter enabled on landing
     *
     * @return bool
     */
    public function splitterEnabled(): bool
    {
        return $this->splitterEnabled === true;
    }

    /**
     * Get all comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Ads, which is attached to this domain
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ads()
    {
        return $this->hasMany(Ad::class, 'domain_id');
    }

    /**
     * Ad accounts, which is attached to this domain
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function accounts()
    {
        return $this->hasManyThrough(
            Account::class,
            Ad::class,
            'domain_id',
            'account_id',
            null,
            'account_id'
        );
    }

    /**
     * Adsets, which is attached to this domain
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function adsets()
    {
        return $this->hasManyThrough(
            AdSet::class,
            Ad::class,
            'domain_id',
            'id',
            null,
            'adset_id'
        );
    }

    /**
     * Adsets, which is attached to this domain
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function campaigns()
    {
        return $this->hasManyThrough(
            Campaign::class,
            Ad::class,
            'domain_id',
            'id',
            null,
            'campaign_id'
        );
    }

    /**
     * Retrieve domain ads with amount of approved ads
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithPassedCount(Builder $builder)
    {
        return $builder->selectSub(
            Ad::whereColumn('domain_id', '=', 'domains.id')->whereHas('cachedInsights')->count(),
            'passed'
        );
    }

    /**
     * Retrieve domain ads with passing status
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithRejectedCount(Builder $builder)
    {
        return $builder->selectSub(
            Ad::whereColumn('domain_id', '=', 'domains.id')->whereDoesnHave('cachedInsights')->count(),
            'rejected'
        );
    }

    /**
     * Transfers domain to specified order
     *
     * @param Order $order
     */
    public function transfer(Order $order)
    {
        $oldOrder = $this->order;
        $this->recordAs(static::TRANSFERRED)
            ->update(['order_id' => $order->id]);

        if ($this->isReady()) {
            $oldOrder->updateProgress();
            $order->updateProgress();
        }
    }

    /**
     * Domain traffic source
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trafficSource()
    {
        return $this->belongsTo(TrafficSource::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'landing_id');
    }
}
