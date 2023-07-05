<?php

namespace App;

use App\Events\Offers\Created;
use App\Events\Offers\Creating;
use App\Integrations\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * App\Offer
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $vertical
 * @property int|null $branch_id
 * @property string|null $description
 * @property string|null $pb_lead
 * @property string|null $pb_sale
 * @property string|null $uuid
 * @property bool|null $generate_email
 * @property bool|null $allow_duplicates
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $allowedUsers
 * @property-read int|null $allowed_users_count
 * @property-read \App\Branch|null $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Result[] $results
 * @property-read int|null $results_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderRoute[] $routes
 * @property-read int|null $routes_count
 *
 * @method static Builder|Offer allowed()
 * @method static Builder|Offer burj()
 * @method static Builder|Offer crypto()
 * @method static Builder|Offer current()
 * @method static Builder|Offer duplicates()
 * @method static Builder|Offer leftovers()
 * @method static Builder|Offer newModelQuery()
 * @method static Builder|Offer newQuery()
 * @method static Builder|Offer query()
 * @method static Builder|Offer resells()
 * @method static Builder|Offer skipsConversion()
 * @method static Builder|Offer whereAllowDuplicates($value)
 * @method static Builder|Offer whereBranchId($value)
 * @method static Builder|Offer whereCreatedAt($value)
 * @method static Builder|Offer whereDescription($value)
 * @method static Builder|Offer whereGenerateEmail($value)
 * @method static Builder|Offer whereId($value)
 * @method static Builder|Offer whereName($value)
 * @method static Builder|Offer wherePbLead($value)
 * @method static Builder|Offer wherePbSale($value)
 * @method static Builder|Offer whereUpdatedAt($value)
 * @method static Builder|Offer whereUuid($value)
 * @method static Builder|Offer whereVertical($value)
 * @method static Builder|Offer withAcceptedCount($date = null)
 * @method static Builder|Offer withCurrentAccountsCount()
 * @method static Builder|Offer withCurrentAffiliateLeadsCount()
 * @method static Builder|Offer withCurrentAffiliateValidLeadsCount()
 * @method static Builder|Offer withCurrentClicksCount()
 * @method static Builder|Offer withCurrentCost()
 * @method static Builder|Offer withCurrentCpc()
 * @method static Builder|Offer withCurrentCpl()
 * @method static Builder|Offer withCurrentCpm()
 * @method static Builder|Offer withCurrentCtr()
 * @method static Builder|Offer withCurrentFbLeadsCount()
 * @method static Builder|Offer withCurrentImpressionsCount()
 * @method static Builder|Offer withCurrentOwnLeadsCount()
 * @method static Builder|Offer withCurrentOwnValidLeadsCount()
 * @method static Builder|Offer withLeftoverCount($date = null)
 * @method static Builder|Offer withOrderedCount($date = null)
 * @method static Builder|Offer withReceivedCount($date = null)
 * @method static Builder|Offer withResellOrderedCount()
 * @method static Builder|Offer withResellReceivedCount()
 * @mixin \Eloquent
 */
class Offer extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'offers';

    /**
     * Guarded model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'generate_email' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'created'  => Created::class,
        'creating' => Creating::class,
    ];

    public const VERTICAL_BURJ = 'Burj';

    public const VERTICAL_CRYPT = 'Crypt';

    public const VERTICALS = [
        self::VERTICAL_BURJ,
        self::VERTICAL_CRYPT,
    ];

    /**
     * Get related results
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Determine is form detected
     *
     * @return bool
     */
    public function hasForm()
    {
        return ! is_null($this->integration_form_id);
    }

    /**
     * Append accepted leads count
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null|array|string                     $date
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeWithAcceptedCount(Builder $builder, $date = null)
    {
        $date = date_between($date);

        return $builder->selectSub(
            Lead::visible()
                ->valid()
                ->selectRaw('count(*) as accepted')
                ->when($date, fn ($query) => $query->whereBetween(DB::raw('created_at::date'), $date))
                ->unless($date, fn ($query) => $query->whereDate('created_at', now()))
                ->whereColumn('offer_id', '=', 'offers.id'),
            'accepted'
        );
    }

    /**
     * Append ordered leads count
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null|array|string                     $date
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeWithOrderedCount(Builder $builder, $date = null)
    {
        return $builder->selectSub(
            LeadOrderRoute::visible()->selectRaw('sum("leadsOrdered")')
                ->when($date, fn ($query) => $query->forDate($date), fn ($query) => $query->current())
                ->whereColumn('offer_id', '=', 'offers.id'),
            'ordered'
        );
    }

    /**
     * Append received count
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null|array|string                     $date
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeWithReceivedCount(Builder $builder, $date = null)
    {
        return $builder->selectSub(
            LeadOrderRoute::visible()->withTrashed()->selectRaw('sum("leadsReceived")')
                ->when($date, fn ($query) => $query->forDate($date))
                ->unless($date, fn ($query) => $query->current())
                ->whereColumn('offer_id', '=', 'offers.id'),
            'received'
        );
    }

    /**
     * Append leftover count
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null|array|string                     $date
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeWithLeftoverCount(Builder $builder, $date = null)
    {
        return $builder->selectSub(
            Lead::visible()
                ->selectRaw('count(*) as leftovers')
                ->leftovers($date)
                ->whereColumn('offer_id', '=', 'offers.id'),
            'leftover'
        );
    }

    /**
     * Appends resell batch ordered count
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithResellOrderedCount(Builder $builder)
    {
        return $builder->selectSub(
            Lead::visible()->selectRaw('coalesce(count(leads.id), 0)')
                ->join('lead_resell_batch', 'leads.id', 'lead_resell_batch.lead_id')
                ->whereColumn('leads.offer_id', '=', 'offers.id'),
            'resell_ordered'
        );
    }

    /**
     * Appends resell batch received count
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithResellReceivedCount(Builder $builder)
    {
        return $builder->selectSub(
            Lead::visible()->selectRaw('coalesce(count(leads.id), 0)')
                ->join('lead_resell_batch', 'leads.id', 'lead_resell_batch.lead_id')
                ->whereColumn('leads.offer_id', '=', 'offers.id')
                ->whereNotNull('lead_resell_batch.assigned_at'),
            'resell_received'
        );
    }

    /**
     * Determine is some offer is related to KZ
     *
     * @return bool
     */
    public function isKz()
    {
        return Str::contains($this->name, 'KZ');
    }

    /**
     * Determines is some offer is not related to kz
     *
     * @return bool
     */
    public function isNotKz()
    {
        return ! $this->isKz();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routes()
    {
        return $this->hasMany(LeadOrderRoute::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentImpressionsCount(Builder $builder)
    {
        return $builder->selectSub(
            Insights::whereDate('date', now())
                ->selectRaw('sum(impressions)')
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_impressions'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentClicksCount(Builder $builder)
    {
        return $builder->selectSub(
            Insights::whereDate('date', now())
                ->selectRaw('sum(link_clicks::int)')
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_clicks'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentAccountsCount(Builder $builder)
    {
        return $builder->selectSub(
            Insights::whereDate('date', now())
                ->selectRaw('count(distinct account_id)')
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_accounts'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCost(Builder $builder)
    {
        return $builder->selectSub(
            Insights::whereDate('date', now())
                ->selectRaw('sum(spend::decimal)')
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_facebook_cost'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentFbLeadsCount(Builder $builder)
    {
        return $builder->selectSub(
            Insights::whereDate('date', now())
                ->selectRaw('sum(leads_cnt)')
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_facebook_leads'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCpl(Builder $builder)
    {
        return $builder->selectSub(
            Insights::selectRaw('round(sum(spend::decimal) / nullif(sum(leads_cnt),0), 2)')
                ->whereDate('date', now())
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_facebook_cpl'
        );
    }


    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCpm(Builder $builder)
    {
        return $builder->selectSub(
            Insights::selectRaw('round(sum(spend::decimal) / (nullif(sum(impressions::decimal), 0) / 1000), 2)')
                ->whereDate('date', now())
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_facebook_cpm'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCpc(Builder $builder)
    {
        return $builder->selectSub(
            Insights::selectRaw('round(sum(spend::decimal) / nullif(sum(link_clicks::decimal),0), 2)')
                ->whereDate('date', now())
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_facebook_cpc'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentCtr(Builder $builder)
    {
        return $builder->selectSub(
            Insights::selectRaw('round(sum(link_clicks::decimal) / nullif(sum(impressions::decimal), 0) * 100, 2)')
                ->whereDate('date', now())
                ->whereColumn('facebook_cached_insights.offer_id', '=', 'offers.id'),
            'current_facebook_ctr'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentOwnLeadsCount(Builder $builder)
    {
        return $builder->selectSub(
            Lead::visible()
                ->selectRaw('count(distinct leads.id)')
                ->whereNull('affiliate_id')
                ->whereDate('created_at', now())
                ->whereColumn('offer_id', '=', 'offers.id'),
            'current_all_own_leads'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentOwnValidLeadsCount(Builder $builder)
    {
        return $builder->selectSub(
            Lead::visible()->valid()
                ->selectRaw('count(distinct leads.id)')
                ->whereNull('affiliate_id')
                ->whereDate('created_at', now())
                ->whereColumn('offer_id', '=', 'offers.id'),
            'current_valid_own_leads'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentAffiliateLeadsCount(Builder $builder)
    {
        return $builder->selectSub(
            Lead::visible()
                ->selectRaw('count(leads.id)')
                ->whereNotNull('affiliate_id')
                ->whereDate('created_at', now())
                ->whereColumn('offer_id', '=', 'offers.id'),
            'current_all_affiliate_leads'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrentAffiliateValidLeadsCount(Builder $builder)
    {
        return $builder->selectSub(
            Lead::visible()->valid()
                ->selectRaw('count(distinct leads.id)')
                ->whereNotNull('affiliate_id')
                ->whereDate('created_at', now())
                ->whereColumn('offer_id', '=', 'offers.id'),
            'current_valid_affiliate_leads'
        );
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowed(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('offers.created_at', '<', '2021-11-05 00:00:00');
        }

        if (
            auth()->user() instanceof User
            && !in_array(auth()->user()->role, [User::ADMIN, User::DEVELOPER, User::DESIGNER])
        ) {
            $builder->whereIn('offers.id', auth()->user()->allowedOffers()->pluck('allowed_offers.offer_id')->values());
        }

        return $builder;
    }

    /**
     * Determines is offer marked for resell
     *
     * @return bool
     */
    public function isResell(): bool
    {
        return Str::endsWith($this->name, '_R');
    }

    /**
     * Determines is offer marked as leftover
     *
     * @return bool
     */
    public function isLeftover(): bool
    {
        return Str::startsWith($this->name, 'LO_');
    }

    /**
     * Determines is offer marked as duplicate
     *
     * @return bool
     */
    public function isDuplicate(): bool
    {
        return Str::endsWith($this->name, '_D');
    }

    /**
     * Determines is offer marked as duplicate
     *
     * @return bool
     */
    public function isColdBase(): bool
    {
        return Str::endsWith($this->name, '_CD');
    }

    /**
     * Leftover offer group
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeLeftovers(Builder $query)
    {
        return $query->where('offers.name', 'like', 'LO\_%');
    }

    /**
     * Resell offer group
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeResells(Builder $query)
    {
        return $query->where('offers.name', 'like', '%\_R');
    }

    /**
     * Duplicate offer scope
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeDuplicates(Builder $query)
    {
        return $query->where('offers.name', 'like', '%\_D');
    }

    /**
     * Current offer group
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeCurrent(Builder $builder)
    {
        return $builder->where('offers.name', 'not like', 'LO\_%')
            ->where('offers.name', 'not like', '%\_R');
    }

    /**
     * Offers that skips on office conversion
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeSkipsConversion(Builder $builder)
    {
        return $builder->where(function ($query) {
            $query->where('offers.name', 'like', 'LO\_%')->orWhere('offers.name', 'like', '%\_R');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'allowed_offers');
    }

    /**
     * Offer leads
     *
     * @return void
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope for crypto offers
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeCrypto(Builder $builder)
    {
        return $builder->where('offers.vertical', self::VERTICAL_CRYPT);
    }

    /**
     * Scope for the burj offers
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeBurj(Builder $builder)
    {
        return $builder->where('offers.vertical', self::VERTICAL_BURJ);
    }

    /**
     * @return static
     */
    public function getLOCopy()
    {
        return $this->isLeftover() ? $this : Offer::firstOrCreate(['name' => sprintf('LO_%s', $this->name)]);
    }

    /**
     * @return static
     */
    public function getOriginalCopy()
    {
        return !$this->isLeftover() ? $this : Offer::firstWhere(['name' => Str::after($this->name, 'LO_')]);
    }

    /**
     * @return static
     */
    public function getResellCopy()
    {
        return $this->isResell() ? $this : Offer::firstOrCreate(['name' => sprintf('%s_R', $this->name)]);
    }

    /**
     * @return static
     */
    public function getOriginalResellCopy()
    {
        return !$this->isResell() ? $this : Offer::firstWhere(['name' => Str::before($this->name, '_R')]);
    }

    /**
     * @return static
     */
    public function getDuplicateCopy()
    {
        return $this->isDuplicate() ? $this : Offer::firstOrCreate(['name' => sprintf('%s_D', $this->name)]);
    }

    /**
     * @return static
     */
    public function getOriginalDuplicateCopy()
    {
        return !$this->isDuplicate() ? $this : Offer::firstWhere(['name' => Str::before($this->name, '_D')]);
    }

    /**
     * @return static
     */
    public function getColdBaseCopy()
    {
        return $this->isColdBase() ? $this : Offer::firstOrCreate(
            ['name' => sprintf('%s_CD', $this->name)],
            Arr::except($this->attributesToArray(), ['name', 'id', 'uuid', 'created_at', 'updated_at'])
        );
    }

    /**
     * @return static
     */
    public function getOriginalColdBaseCopy()
    {
        return !$this->isColdBase() ? $this : Offer::firstWhere(['name' => Str::before($this->name, '_CD')]);
    }

    public function hasLeadPostback():bool
    {
        return !is_null($this->pb_lead);
    }

    public function hasDepositPostback():bool
    {
        return !is_null($this->pb_sale);
    }
}
