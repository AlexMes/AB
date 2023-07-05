<?php

namespace App;

use App\Binom\Click;
use App\Events\Lead\Created;
use App\Events\Lead\Updated;
use App\Facebook\Account;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Integrations\Payload;
use App\Jobs\Leads\DetectAccount;
use App\Jobs\Leads\DetectBuyer;
use App\Jobs\Leads\DetectCampaign;
use App\Jobs\Leads\DetectGender;
use App\Jobs\Leads\DetectOffer;
use App\Jobs\Leads\DetectTrafficSource;
use App\Jobs\Leads\FetchIpAddressData;
use App\Jobs\PullClickInfo;
use App\Leads\PoolAnswer;
use App\Traits\RecordEvents;
use App\Traits\SaveQuietly;
use Faker\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * App\Lead
 *
 * @property int $id
 * @property void $domain
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $ip
 * @property string|null $form_type
 * @property string|null $utm_source
 * @property string|null $utm_content
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_medium
 * @property string|null $clickid
 * @property array|null $requestData
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $offer_id
 * @property string|null $account_id
 * @property int|null $user_id
 * @property string|null $adset_id
 * @property string|null $campaign_id
 * @property int|null $landing_id
 * @property bool $valid
 * @property int|null $gender_id
 * @property string|null $middlename
 * @property int|null $affiliate_id
 * @property string|null $uuid
 * @property bool|null $phone_valid
 * @property int|null $traffic_source_id
 * @property array|null $poll
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $app_id
 * @property string|null $traffic_source
 * @property bool $recreate_deposit
 * @property-read Account|null $account
 * @property-read AdSet|null $adset
 * @property-read \App\Affiliate|null $affiliate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderAssignment[] $assignments
 * @property-read int|null $assignments_count
 * @property-read Campaign|null $campaign
 * @property-read Click|null $click
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Deposit[] $deposits
 * @property-read int|null $deposits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read \App\LeadOrderAssignment|null $current_assignment
 * @property-read void $current_time
 * @property-read string $formatted_phone
 * @property-read string $fullname
 * @property-read mixed $gender
 * @property-read bool $is_duplicate
 * @property-read \App\IpAddress|null $ipAddress
 * @property-read \App\IpAddress|null $ipLocation
 * @property-read \App\Domain|null $landing
 * @property-read \App\LeadOrderAssignment|null $lastAssignment
 * @property-read \App\PhoneLookup|null $lookup
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadMarker[] $markers
 * @property-read int|null $markers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SmsMessage[] $messages
 * @property-read int|null $messages_count
 * @property-read \App\Offer|null $offer
 * @property-read \Illuminate\Database\Eloquent\Collection|Payload[] $payloads
 * @property-read int|null $payloads_count
 * @property-read \App\PhoneLookup $phoneLookup
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderRoute[] $routes
 * @property-read int|null $routes_count
 * @property-read \App\TrafficSource|null $trafficSource
 * @property-read \App\User|null $user
 *
 * @method static Builder|Lead accepted($date = null, $offer = null)
 * @method static Builder|Lead allowedBranches()
 * @method static Builder|Lead allowedOffers()
 * @method static Builder|Lead assigned()
 * @method static Builder|Lead duplicates($date = null, $offer = null)
 * @method static Builder|Lead fromAffiliates()
 * @method static Builder|Lead leftovers($date = null, $offer = null)
 * @method static Builder|Lead newModelQuery()
 * @method static Builder|Lead newQuery()
 * @method static \Illuminate\Database\Query\Builder|Lead onlyTrashed()
 * @method static Builder|Lead own()
 * @method static Builder|Lead query()
 * @method static Builder|Lead received($date = null, $offer = null)
 * @method static Builder|Lead resellReceived()
 * @method static Builder|Lead unassigned()
 * @method static Builder|Lead valid()
 * @method static Builder|Lead visible()
 * @method static Builder|Lead whereAccountId($value)
 * @method static Builder|Lead whereAdsetId($value)
 * @method static Builder|Lead whereAffiliateId($value)
 * @method static Builder|Lead whereAppId($value)
 * @method static Builder|Lead whereCampaignId($value)
 * @method static Builder|Lead whereClickid($value)
 * @method static Builder|Lead whereCreatedAt($value)
 * @method static Builder|Lead whereDeletedAt($value)
 * @method static Builder|Lead whereDomain($value)
 * @method static Builder|Lead whereEmail($value)
 * @method static Builder|Lead whereFirstname($value)
 * @method static Builder|Lead whereFormType($value)
 * @method static Builder|Lead whereGenderId($value)
 * @method static Builder|Lead whereId($value)
 * @method static Builder|Lead whereIp($value)
 * @method static Builder|Lead whereLandingId($value)
 * @method static Builder|Lead whereLastname($value)
 * @method static Builder|Lead whereMiddlename($value)
 * @method static Builder|Lead whereOfferId($value)
 * @method static Builder|Lead wherePhone($value)
 * @method static Builder|Lead wherePhoneValid($value)
 * @method static Builder|Lead wherePoll($value)
 * @method static Builder|Lead whereRecreateDeposit($value)
 * @method static Builder|Lead whereRequestData($value)
 * @method static Builder|Lead whereTrafficSource($value)
 * @method static Builder|Lead whereTrafficSourceId($value)
 * @method static Builder|Lead whereUpdatedAt($value)
 * @method static Builder|Lead whereUserId($value)
 * @method static Builder|Lead whereUtmCampaign($value)
 * @method static Builder|Lead whereUtmContent($value)
 * @method static Builder|Lead whereUtmMedium($value)
 * @method static Builder|Lead whereUtmSource($value)
 * @method static Builder|Lead whereUtmTerm($value)
 * @method static Builder|Lead whereUuid($value)
 * @method static Builder|Lead whereValid($value)
 * @method static \Illuminate\Database\Query\Builder|Lead withTrashed()
 * @method static Builder|Lead withoutCopies()
 * @method static Builder|Lead withoutDeliveryFailed($destinationId = null)
 * @method static \Illuminate\Database\Query\Builder|Lead withoutTrashed()
 * @mixin \Eloquent
 */
class Lead extends Model
{
    use SaveQuietly;
    use RecordEvents;
    use SoftDeletes;

    public const OFFER_DETECTED          = 'offer_detected';
    public const ACCOUNT_DETECTED        = 'account_detected';
    public const BUYER_DETECTED          = 'buyer_detected';
    public const GENDER_DETECTED         = 'gender_detected';
    public const IP_FETCHED              = 'ip_fetched';
    public const CLICK_INFO_PULLED       = 'click_info_pulled';
    public const CAMPAIGN_DETECTED       = 'campaign_detected';
    public const ASSIGNED                = 'assigned';
    public const TRANSFERRED             = 'transferred';
    public const ROUTE_TRANSFERRED       = 'route_transferred';
    public const PHONE_VALIDATED         = 'phone_validated';
    public const CALLED                  = 'called';
    public const CALLED_ALT              = 'called_alt';
    public const UNASSIGNED              = 'unassigned';
    public const TRAFFIC_SOURCE_DETECTED = 'traffic_source_detected';
    public const RESTORED                = 'restored';
    public const MARKER_DELETED          = 'marker_deleted';
    public const DELIVERY_TRY            = 'delivery_try';
    public const DELIVERED               = 'delivered';
    public const DELIVERY_FAILED         = 'delivery_failed';
    public const COPIED                  = 'copied';
    public const COPY                    = 'copy';

    /**
     * Mapping int to gender
     *
     * @var array
     */
    public const GENDERS = [
        0 => 'Неизвестный',
        1 => 'Мужской',
        2 => 'Женский',
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'leads';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Hide attributes from JSON
     *
     * @var string[]
     */
    protected $hidden = [
        'requestData'
    ];

    /**
     * Cast model attributes to native type
     *
     * @var array
     */
    protected $casts = [
        'requestData' => 'array',
        'valid'       => 'bool',
        'poll'        => 'array',
    ];

    /**
     * Append attributes
     *
     * @var string[]
     */
    protected $appends = [
        'fullname'
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Created::class,
        'updated' => Updated::class,
    ];

    /**
     * @param array $data
     *
     * @return static
     */
    public static function makeForTestDestination(array $data = [])
    {
        $realLead = static::valid()
            ->whereHas('ipAddress', fn ($query) => $query->where('country_code', $data['geo']))
            ->whereHas('lookup', fn ($query) => $query->where('country', $data['geo']))
            ->orderByRaw('random()')
            ->limit(1)
            ->first();

        $lastPhoneDigits = Str::substr($realLead->phone, Str::length($realLead->phone) - 2);

        $lastIpDigits = Str::afterLast(
            $realLead->ip,
            filter_var($realLead->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? '.' : ':'
        );
        $lastIpDigits = Str::substr($lastIpDigits, Str::length($lastIpDigits) - 2);

        $lead = new static([
            'firstname' => 'Integration',
            'lastname'  => 'Mint',
            'email'     => Str::random() . '@gmail.com',
            'offer_id'  => $data['offer_id'],
            'phone'     => Str::replaceLast(
                $lastPhoneDigits,
                Str::padLeft(abs(rand(55, 99) - $lastPhoneDigits), 2, '0'),
                $realLead->phone
            ),
            'ip' => Str::replaceLast(
                $lastIpDigits,
                max(1, abs(rand(55, 99) - preg_replace('~[A-z]~', '3', $lastIpDigits))),
                $realLead->ip
            ),
            'poll' => $realLead->poll,
        ]);

        return $lead;
    }

    /**
     * Related lead adset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adset()
    {
        return $this->belongsTo(AdSet::class, 'adset_id');
    }

    /**
     * Related lead adset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    /**
     * Related lead adset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    /**
     * Related lead adset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    /**
     * Related lead adset
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Assigned messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(SmsMessage::class, 'lead_id')
            ->orderByDesc('created_at');
    }

    /**
     * Landing page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function landing()
    {
        return $this->belongsTo(Domain::class, 'landing_id');
    }

    /**
     * Get full lead name
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return implode(" ", array_diff([$this->firstname,$this->middlename,$this->lastname], ['', null, false]));
    }

    /**
     * Format phone number
     *
     * @param string $phone
     *
     * @return void
     */
    public function setPhoneAttribute($phone)
    {
        $this->attributes['phone'] = digits($phone);
    }

    /**
     * get only valid leads
     *
     * @param $query
     *
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeValid($query)
    {
        return $query->where('valid', true);
    }

    /**
     * Run set of binding jobs
     *
     * @return void
     */
    public function bindBoardAttributes()
    {
        if ($this->affiliate_id === null) {
            DetectCampaign::withChain([
                new DetectOffer($this),
                new DetectAccount($this),
                new DetectBuyer($this),
                new DetectGender($this),
                new FetchIpAddressData($this),
                new PullClickInfo($this),
                new DetectTrafficSource($this),
            ])->dispatch($this)->allOnQueue(AdsBoard::QUEUE_DEFAULT);
        }
    }

    /**
     * Determine is lead valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Determine is landing page detected
     *
     * @return bool
     */
    public function hasLanding()
    {
        return ! is_null($this->landing_id);
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
     * Get formatted gender attribute
     *
     * @return mixed
     */
    public function getGenderAttribute()
    {
        return self::GENDERS[$this->gender_id] ?? 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * all integration payloads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payloads()
    {
        return $this->hasMany(Payload::class);
    }

    /**
     * Determines when current lead was sent to external CRM repeatedly
     *
     * @return bool
     */
    public function getIsDuplicateAttribute()
    {
        return $this->payloads
            ->map(function ($payload) {
                return optional(collect(optional(json_decode($payload->responseContents))->fields)
                    ->pluck('params', 'unique')
                    ->get('telefon'))
                    ->duplicate;
            })
            ->filter(function ($duplicate) {
                return ! is_null($duplicate);
            })
            ->isEmpty() === false;
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
     * Get location info
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ipLocation()
    {
        return $this->belongsTo(IpAddress::class, 'ip', 'ip');
    }

    /**
     * Lead-manager assignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignments()
    {
        return $this->hasMany(LeadOrderAssignment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastAssignment()
    {
        return $this->hasOne(LeadOrderAssignment::class)->orderBy('created_at');
    }

    /**
     * Scope unassigned leads
     *
     * @param Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnassigned(Builder $builder)
    {
        return $builder->whereDoesntHave('assignments');
    }

    /**
     * Scope assigned leads
     *
     * @param Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssigned(Builder $builder)
    {
        return $builder->whereHas('assignments');
    }

    /**
     * Scope leftover leads
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null|mixed                            $date
     * @param null|mixed                            $offer
     *
     * @return mixed
     */
    public function scopeLeftovers(Builder $builder, $date = null, $offer = null)
    {
        $date = date_between($date);

        return $builder->unassigned()
            ->valid()
            ->notEmptyWhereIn('offer_id', $offer)
            ->when($date, fn ($query) => $query->whereBetween(DB::raw('leads.created_at::date'), $date))
            ->unless($date, fn ($query) => $query->whereDate('leads.created_at', '>=', '2022-01-01'));
    }

    /**
     * Scope received leads
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null|mixed                            $date
     * @param null                                  $offer
     *
     * @return mixed
     */
    public function scopeReceived(Builder $builder, $date = null, $offer = null)
    {
        $date = date_between($date);

        return $builder->notEmptyWhereIn(
            'id',
            LeadOrderAssignment::query()
                ->whereIn('route_id', LeadOrderRoute::query()
                ->notEmptyWhere('offer_id', $offer)
                ->when($date, fn ($query) => $query->forDate($date))
                ->unless($date, fn ($query) => $query->current())
                ->pluck('id'))
                ->pluck('lead_id')
                ->toArray()
        );
    }

    /**
     * Scope accepted leads
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null|mixed                            $date
     * @param null                                  $offer
     *
     * @return mixed
     */
    public function scopeAccepted(Builder $builder, $date = null, $offer = null)
    {
        $date = date_between($date);

        return $builder->valid()
            ->notEmptyWhereIn(
                'id',
                LeadOrderAssignment::whereIn('route_id', LeadOrderRoute::query()
                    ->notEmptyWhere('offer_id', $offer)
                    ->when($date, fn ($query) => $query->forDate($date))
                    ->unless($date, fn ($query) => $query->current())
                    ->pluck('id'))
                    ->pluck('lead_id')
                    ->toArray()
            );
    }

    /**
     * Scope resell received leads
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeResellReceived(Builder $builder)
    {
        return $builder->whereExists(function ($query) {
            return $query->selectRaw(1)
                ->from('lead_resell_batch')
                ->whereColumn('lead_resell_batch.lead_id', '=', 'leads.id')
                ->whereNotNull('lead_resell_batch.assigned_at');
        });
    }

    /**
     * Ip address related to the lead
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ipAddress()
    {
        return $this->belongsTo(IpAddress::class, 'ip', 'ip');
    }

    public function phoneLookup()
    {
        return $this->belongsTo(PhoneLookup::class, );
    }

    /**
     * Determine is lead have been converted
     *
     * @return bool
     */
    public function hasDeposits()
    {
        return $this->deposits->count() > 0;
    }

    /**
     * Related click information from Binom
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function click()
    {
        return $this->hasOne(Click::class);
    }

    /**
     * Format phone number for the managers
     *
     * @return string
     */
    public function getFormattedPhoneAttribute()
    {
        return (mb_substr($this->phone, 0, 1) == 8
        && mb_substr($this->phone, 0, 2) != 88
        && mb_substr($this->phone, 0, 2) != 82)
            ? substr_replace($this->phone, 7, 0, 1) :
            $this->phone;
    }

    /**
     * Lead affiliate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Determines is lead has affiliate
     *
     * @return bool
     */
    public function hasAffiliate()
    {
        return $this->affiliate_id !== null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trafficSource()
    {
        return $this->belongsTo(TrafficSource::class);
    }

    /**
     * Get original, or make up fake email address
     * for the lead
     *
     * @return string
     */
    public function getOrGenerateEmail(): string
    {
        return $this->email ?? Str::slug($this->fullname, '') . '@' . Arr::random(['gmail.com']);
    }

    /**
     * Gets current assignment
     *
     * @return LeadOrderAssignment|null
     */
    public function getCurrentAssignmentAttribute(): ?LeadOrderAssignment
    {
        if (!isset($this->attributes['current_assignment'])) {
            $this->attributes['current_assignment'] = $this->getAssignment();
        }

        return $this->attributes['current_assignment'];
    }

    /**
     * @param Manager|null $manager
     *
     * @return LeadOrderAssignment|null
     */
    public function getAssignment(?Manager $manager = null): ?LeadOrderAssignment
    {
        return $this->assignments()
            ->when(
                $manager !== null,
                fn ($query) => $query->whereHas('route', fn ($q) => $q->whereManagerId($manager->id))
            )
            ->latest('id')
            ->first();
    }

    /**
     * Leads from affiliate traffic only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromAffiliates(Builder $builder)
    {
        return $builder->whereNotNull('affiliate_id');
    }

    /**
     * Leads from own traffic only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwn(Builder $builder)
    {
        return $builder->whereNull('affiliate_id');
    }

    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->hasRole([User::ADMIN,User::DEVELOPER])) {
            return $builder;
        }

        if (auth()->id() === 230) {
            // jrd old access
            return $builder->where('leads.created_at', '<', '2021-11-05 00:00:00');
        }

        if (auth()->user()->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            // scope support to visible users
            return $builder->where(function (Builder $query) {
                return $query->whereIn('leads.user_id', User::visible()->pluck('users.id'))
                    ->orWhereNull('leads.user_id')
                    ->orWhereIn('leads.affiliate_id', Affiliate::visible()->pluck('affiliates.id'));
            })->whereIn('leads.offer_id', Offer::allowed()->pluck('offers.id'));
        }

        if (! auth()->user()->isAdmin()) {
            return $builder->joinSub(User::visible()->select(['id','branch_id']), 'visible', 'leads.user_id', '=', 'visible.id');
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        if (
            auth()->user() instanceof User
            && !in_array(auth()->user()->role, [User::ADMIN, User::DEVELOPER, User::DESIGNER])
        ) {
            $builder->whereIn('leads.offer_id', auth()->user()->allowedOffers->pluck('id')->values())->orWhereIn('leads.offer_id', Offer::leftovers()->pluck('id'));
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedBranches(Builder $builder)
    {
        if (auth()->user()->isDesigner()) {
            return $builder->whereIn(
                'leads.user_id',
                User::forAllowedBranches()->select('id')->pluck('id'),
            );
        }

        return $builder;
    }

    /**
     * Scopes leads without copies and not copies of original
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithoutCopies(Builder $builder)
    {
        return $builder->whereDoesntHave('events', function ($query) {
            return $query->where('type', static::COPIED)
                ->orWhere('type', static::COPY);
        });
    }

    /**
     * @param Builder $builder
     * @param null    $date
     * @param null    $offer
     *
     * @return Builder
     */
    public function scopeDuplicates(Builder $builder, $date = null, $offer = null)
    {
        $date = date_between($date);

        return $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) use ($date, $offer) {
            return $query->selectRaw(1)
                ->from('leads', 'duplicates')
                ->whereColumn('leads.phone', 'duplicates.phone')
                ->whereColumn('leads.id', '!=', 'duplicates.id')
                ->whereNull('duplicates.deleted_at')
                ->when($date, fn ($q) => $q->whereBetween(DB::raw('duplicates.created_at::date'), $date))
                ->when($offer, fn ($q) => $q->whereIn('duplicates.offer_id', Arr::wrap($offer)));
        });
    }

    /**
     * @param Builder $builder
     * @param $destinationId
     *
     * @return Builder
     */
    public function scopeWithoutDeliveryFailed(Builder $builder, $destinationId = null)
    {
        return $builder->whereDoesntHave('events', function ($query) use ($destinationId) {
            return $query->where('type', Lead::DELIVERY_FAILED)
                ->when($destinationId, fn ($q) => $q->where(
                    DB::raw('custom_data::text'),
                    'like',
                    '%"destination_id":' . $destinationId . ',%'
                ));
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function routes()
    {
        return $this->hasManyThrough(
            LeadOrderRoute::class,
            LeadOrderAssignment::class,
            'lead_id',
            'id',
            'id',
            'route_id'
        );
    }

    /**
     * Determine is lead has poll
     * available
     *
     * @return bool
     */
    public function hasPoll()
    {
        return $this->attributes['poll'] !== null || data_get($this->requestData, 'answers', null) !== null;
    }

    /**
     * Cast poll results into object
     *
     * @return \Illuminate\Support\Collection
     */
    public function pollResults(): Collection
    {
        if ($this->hasPoll()) {
            try {
                $polldata = $this->poll ?? data_get($this->requestData, 'answers', null);
                if ($polldata !== null && ! is_array($polldata)) {
                    $polldata = json_decode($polldata, true);
                }

                return collect($polldata)->mapInto(PoolAnswer::class)->reject(function (PoolAnswer $answer) {
                    return in_array($answer->getQuestion(), ['name','h-captcha-response','g-recaptcha-response','firstname','lastname','phone','email','Unavailable','code']);
                });
            } catch (\Throwable $exception) {
                return collect();
            }
        }

        return collect();
    }

    /**
     * String representation of the poll
     *
     * @return string|null
     */
    public function getPollAsText(): ?string
    {
        if (!$this->hasPoll()) {
            return null;
        }

        return preg_replace(
            '/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u',
            '',
            $this->pollResults()->reduce(function ($a, PoolAnswer $answer) {
                return $a . $answer->getQuestion() . "\n" . $answer->getAnswer() . "\n";
            }, '')
        );
    }

    /**
     * @return string
     */
    public function getPollAsUrl(): string
    {
//        if (Offer::whereBranchId(19)->whereId($this->offer_id)->exists()) {
//            return URL::temporarySignedRoute('crm.quiz', now()->addWeeks(2), ['uuid' => $this->uuid]);
//        }

        return URL::signedRoute('crm.quiz', ['uuid' => $this->uuid]);
    }

    /**
     * Various markers for the lead
     *
     * @return void
     */
    public function markers()
    {
        return $this->hasMany(LeadMarker::class);
    }

    /**
     * Phone number lookup results
     *
     * @return void
     */
    public function lookup()
    {
        return $this->hasOne(PhoneLookup::class, 'phone', 'phone');
    }

    /**
     * Determine if lead has given marker
     *
     * @param string $marker
     *
     * @return mixed
     */
    public function hasMarker(string $marker)
    {
        return $this->markers()->whereName($marker)->exists();
    }

    /**
     * Marks lead as LO
     *
     * @throws \Throwable
     */
    public function markAsLeftover()
    {
        if (!$this->offer->isLeftover()) {
            $offer = $this->offer->getLOCopy();

            DB::transaction(function () use ($offer) {
                $this->routes()->withTrashed()
                    ->where('offer_id', $this->offer_id)
                    ->update([
                        'offer_id' => $offer->id,
                    ]);

                $this->update([
                    'offer_id' => $offer->id,
                ]);
            });
        }
    }

    /**
     * Move lead under LO
     *
     * @return void
     */
    public function toLeftover()
    {
        if ($this->offer->isLeftover()) {
            return;
        }
        DB::transaction(function () {
            $newOffer = $this->offer->getLOCopy();

            $this->recordAs('moved-to-leftovers')->update(['offer_id' => $newOffer->id]);

            if ($this->hasAssignments()) {
                $this->assignments()->each(function (LeadOrderAssignment $assignment) use ($newOffer) {
                    $route = $assignment->route;

                    $newRoute = LeadOrderRoute::withTrashed()->firstOrCreate([
                        'offer_id'   => $newOffer->id,
                        'order_id'   => $route->order_id,
                        'manager_id' => $route->manager_id,
                    ], ['destination_id' => $route->destination_id]);

                    $assignment->update(['route_id' => $newRoute->id]);
                    $newRoute->recalculate();
                    $route->recalculate();
                });
            }
        });
    }

    /**
     * Move lead to normal offer
     *
     * @return void
     */
    public function fromLeftover()
    {
        if (! $this->offer->isLeftover()) {
            return;
        }
        DB::transaction(function () {
            $newOffer = $this->offer->getOriginalCopy();

            $this->recordAs('moved-from-leftovers')->update(['offer_id' => $newOffer->id]);

            if ($this->hasAssignments()) {
                $this->assignments()->each(function (LeadOrderAssignment $assignment) use ($newOffer) {
                    $route = $assignment->route;

                    $newRoute = LeadOrderRoute::withTrashed()->firstOrCreate([
                        'offer_id'   => $newOffer->id,
                        'order_id'   => $route->order_id,
                        'manager_id' => $route->manager_id,
                    ], ['destination_id' => $route->destination_id]);

                    $assignment->update(['route_id' => $newRoute->id]);
                    $newRoute->recalculate();
                    $route->recalculate();
                });
            }
        });
    }

    /**
     * @return static|null
     */
    public function createDuplicate()
    {
        if ($this->offer->isDuplicate()) {
            return null;
        }

        $newLead       = new static(Arr::except($this->getAttributes(), ['id', 'created_at', 'updated_at']));
        $newLead->uuid = $this->uuid !== null ? Str::uuid()->toString() : null;

        $newLead->offer_id = $this->offer->getDuplicateCopy()->id;

        $newLead->saveQuietly();

        return $newLead;
    }

    public function hasAssignments()
    {
        return $this->assignments()->count() > 0;
    }

    /**
     * Get current time for the lead
     *
     * @return void
     */
    public function getCurrentTimeAttribute()
    {
        if (is_array($this->requestData) && array_key_exists('offset', $this->requestData) && $this->requestData['offset'] !== null) {
            return now('UTC')->subMinutes($this->requestData['offset']);
        }

        return null;
    }

    /**
     * Get domain for the lead
     *
     * @param [type] $domain
     *
     * @return void
     */
    public function getDomainAttribute($domain)
    {
        if ($domain === null) {
            return 'landing-missing.com';
        }

        if (optional($this->user)->branch_id === 19) {
            return 'faguxou3.xyz';
        }

        if (
            optional($this->user)->branch_id === 24
            && in_array(optional($this->offer)->name, [
                "GAZQUIZYAN",
                "GAZQZLPRU",
                "GAZPRLPRU",
                "GAZQUIZNG",
                "GAZQUIZNEW",
                "GAZQUIZOLD",
                "GAZQUIZLPRU",
                "GAZQUIZ_RU_PUSH",
                "GAZRUS_PUSH"
            ])
        ) {
            return 'pydazyo.info';
        }

        return $domain;
    }

    public function getIpAttribute($value)
    {
        if ($value === null) {
            $ipAddress = null;
            foreach (
                IpAddress::whereCountryCode(optional($this->lookup)->country)
                    ->inRandomOrder()
                    ->limit(50)
                    ->get(['ip']) as $row
            ) {
                if (Validator::make(['ip' => $row->ip], ['ip' => 'required|ip'])->passes() && $row->ip != '127.0.0.1') {
                    $ipAddress = $row->ip;
                    break;
                }
            }
            $ipAddress = $ipAddress ?? Factory::create()->ipv4();

            $this->update([
                'ip' => $ipAddress,
            ]);

            return $ipAddress;
        }


        return $value;
    }

    /**
     * @param array|null $click
     *
     * @return bool
     */
    public function saveAppIdFromClick(?array $click): bool
    {
        if (!empty($click)) {
            $needKey = null;
            foreach ($click as $key => $value) {
                if (in_array($value, ['app_id', 'bundle', 'bundle_id'])) {
                    $needKey = $key;
                }
            }

            $needKey = str_replace('_name', '_value', $needKey);
            if (!empty($needKey) && isset($click[$needKey])) {
                $this->app_id = $click[$needKey];

                $this->saveQuietly();

                return true;
            }
        }

        return false;
    }

    /**
     * @param array|null $click
     *
     * @return bool
     */
    public function saveTrafficSourceFromClick(?array $click): bool
    {
        if (!empty($click) && isset($click['traffic_source_name'])) {
            $this->traffic_source = $click['traffic_source_name'];
            $this->saveQuietly();

            return true;
        }

        return false;
    }

    /**
     * @param array $params
     *
     * @return Lead|null
     */
    public function createCopyToOffer(array $params): ?Lead
    {
        if (empty($params['domain_to']) || empty($params['offer_to']) || empty($params['user_to'])) {
            return null;
        }

        $leadCopy = $this->replicate()
            ->fill([
                'utm_source'   => null,
                'utm_campaign' => null,
                'utm_content'  => null,
                'utm_medium'   => null,
                'account_id'   => null,
                'campaign_id'  => null,
                'domain'       => $params['domain_to'],
                'offer_id'     => $params['offer_to'],
                'user_id'      => $params['user_to'],
                'uuid'         => Str::uuid()->toString(),
            ]);
        $leadCopy->save();

        $this->addEvent(static::COPIED, ['copy_id' => $leadCopy->id]);
        $leadCopy->addEvent(static::COPY, ['original_id' => $this->id]);

        return $leadCopy;
    }

    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = Str::before($value, ',');
    }
}
