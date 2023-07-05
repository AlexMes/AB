<?php

namespace App;

use App\Events\OrderRouteCreated;
use App\Events\OrderRouteUpdated;
use App\Jobs\ChangeLeadOrderRouteOffer;
use App\Jobs\TransferLeadOrderRoute;
use App\Leads\AssignLeadToRoute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\LeadOrderRoute
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $manager_id
 * @property int $offer_id
 * @property int $leadsOrdered
 * @property int $leadsReceived
 * @property string|null $last_received_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $status
 * @property int|null $destination_id
 * @property string|null $progress
 * @property string|null $start_at
 * @property string|null $stop_at
 * @property bool $priority
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderAssignment[] $assignments
 * @property-read int|null $assignments_count
 * @property-read \App\LeadDestination|null $destination
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read \App\Manager|null $manager
 * @property-read \App\Offer $offer
 * @property-read \App\LeadsOrder $order
 *
 * @method static Builder|LeadOrderRoute active()
 * @method static Builder|LeadOrderRoute allowedLive(?\Illuminate\Support\Carbon $date = null)
 * @method static Builder|LeadOrderRoute completed()
 * @method static Builder|LeadOrderRoute current()
 * @method static Builder|LeadOrderRoute excludeCountryCondition(?string $geo, ?int $offerId)
 * @method static Builder|LeadOrderRoute excludeManagersWithLead(?\App\Lead $lead = null)
 * @method static Builder|LeadOrderRoute excludeMarkersCondition(array $markers)
 * @method static Builder|LeadOrderRoute excludeOffices($except = null)
 * @method static Builder|LeadOrderRoute excludeOfficesWithCompletedPayments()
 * @method static Builder|LeadOrderRoute forDate($date = null)
 * @method static Builder|LeadOrderRoute incomplete()
 * @method static Builder|LeadOrderRoute newModelQuery()
 * @method static Builder|LeadOrderRoute newQuery()
 * @method static Builder|LeadOrderRoute onlyOffices($only = null)
 * @method static \Illuminate\Database\Query\Builder|LeadOrderRoute onlyTrashed()
 * @method static Builder|LeadOrderRoute paused()
 * @method static Builder|LeadOrderRoute priority()
 * @method static Builder|LeadOrderRoute query()
 * @method static Builder|LeadOrderRoute skipLiveInterval()
 * @method static Builder|LeadOrderRoute visible()
 * @method static Builder|LeadOrderRoute whereCreatedAt($value)
 * @method static Builder|LeadOrderRoute whereDeletedAt($value)
 * @method static Builder|LeadOrderRoute whereDestinationId($value)
 * @method static Builder|LeadOrderRoute whereId($value)
 * @method static Builder|LeadOrderRoute whereLastReceivedAt($value)
 * @method static Builder|LeadOrderRoute whereLeadsOrdered($value)
 * @method static Builder|LeadOrderRoute whereLeadsReceived($value)
 * @method static Builder|LeadOrderRoute whereManagerId($value)
 * @method static Builder|LeadOrderRoute whereOfferId($value)
 * @method static Builder|LeadOrderRoute whereOrderId($value)
 * @method static Builder|LeadOrderRoute wherePriority($value)
 * @method static Builder|LeadOrderRoute whereProgress($value)
 * @method static Builder|LeadOrderRoute whereStartAt($value)
 * @method static Builder|LeadOrderRoute whereStatus($value)
 * @method static Builder|LeadOrderRoute whereStopAt($value)
 * @method static Builder|LeadOrderRoute whereUpdatedAt($value)
 * @method static Builder|LeadOrderRoute withOrderProgress()
 * @method static \Illuminate\Database\Query\Builder|LeadOrderRoute withTrashed()
 * @method static Builder|LeadOrderRoute withoutAutologin()
 * @method static \Illuminate\Database\Query\Builder|LeadOrderRoute withoutTrashed()
 * @mixin \Eloquent
 */
class LeadOrderRoute extends Model
{
    use SoftDeletes;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_PAUSED = 'PAUSED';
    public const STATUSES      = [
        self::STATUS_ACTIVE,
        self::STATUS_PAUSED,
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Bind model attributes
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => OrderRouteCreated::class,
        'updated' => OrderRouteUpdated::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'leadsOrdered'  => 'int',
        'leadsReceived' => 'int',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(LeadsOrder::class, 'order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(Manager::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent(Builder $builder)
    {
        return $builder->whereIn('order_id', LeadsOrder::current()->pluck('id')->values());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplete(Builder $builder)
    {
        return $builder->whereColumn('leadsOrdered', '>', 'leadsReceived');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted(Builder $builder)
    {
        return $builder->whereColumn('leadsOrdered', '=', 'leadsReceived');
    }

    /**
     * Determine is this route complete
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->leadsReceived >= $this->leadsOrdered;
    }

    /**
     * Determine is this order incomplete
     *
     * @return bool
     */
    public function isIncomplete(): bool
    {
        return ! $this->isCompleted();
    }

    /**
     * Lock route for action
     *
     * @return void
     */
    public function lock()
    {
        return Cache::lock(
            sprintf('order-lock-%d', $this->id),
            $this->offer_id === 40 ? 10 * Carbon::SECONDS_PER_MINUTE : 1
        )->get();
    }

    /**
    * @param \Illuminate\Database\Eloquent\Builder $builder
    * @param null|mixed                            $date
    *
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeForDate(Builder $builder, $date = null)
    {
        $date = date_between($date);

        return $builder->whereIn(
            'order_id',
            LeadsOrder::query()
                ->when($date, fn ($query) => $query->whereBetween('date', $date))
                ->unless($date, fn ($query) => $query->whereDate('date', now()))
                ->pluck('id')
                ->values()
        );
    }

    /**
     * Assignments on route
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignments()
    {
        return $this->hasMany(LeadOrderAssignment::class, 'route_id');
    }

    /**
     * Leads pushed for this route
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function leads()
    {
        return $this->hasManyThrough(
            Lead::class,
            LeadOrderAssignment::class,
            'route_id',
            'id',
            'id',
            'lead_id'
        );
    }

    /**
     * Skip routes attached to offices with completed payments
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeExcludeOfficesWithCompletedPayments(Builder $builder)
    {
        $offices = Office::completedPayments()->pluck('id');

        if ($offices->isNotEmpty()) {
            $builder->excludeOffices($offices->toArray());
        }

        return $builder;
    }

    /**
     * Skip routes by office country conditions
     *
     * @param Builder     $builder
     * @param string|null $geo
     * @param int         $offerId
     *
     * @return Builder
     */
    public function scopeExcludeCountryCondition(Builder $builder, ?string $geo, ?int $offerId)
    {
        if ($offerId === null) {
            return $builder;
        }

        $deniedOffices = DistributionRule::getDeniedOffices($geo, $offerId);

        if ($deniedOffices->isNotEmpty()) {
            $builder->excludeOffices($deniedOffices->toArray());
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     * @param array   $markers
     *
     * @return Builder
     */
    public function scopeExcludeMarkersCondition(Builder $builder, array $markers)
    {
        if (in_array('caucasian', $markers)) {
            return $builder->whereDoesntHave('order', function ($qOrder) {
                return $qOrder->whereDoesntHave('office', function ($qOffice) {
                    return $qOffice->where('disallow_caucasian', false);
                });
            });
        }

        return $builder;
    }

    /**
     * Skip routes attached to certain offices
     *
     * @param [type] $query
     * @param [type] $except
     *
     * @return void
     */
    public function scopeExcludeOffices($query, $except = null)
    {
        if ($except !== null) {
            return $query->whereDoesntHave('order', fn ($q) => $q->whereIn('office_id', Arr::wrap($except)));
        }

        return $query;
    }

    /**
     * Inlcude routes only for certain offices
     *
     * @param [type] $query
     * @param [type] $only
     *
     * @return void
     */
    public function scopeOnlyOffices($query, $only = null)
    {
        if ($only !== null && $only !== []) {
            return $query->whereHas('order', fn ($q) => $q->whereIn('office_id', Arr::wrap($only)));
        }

        return $query;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->where('status', self::STATUS_ACTIVE);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePaused(Builder $builder)
    {
        return $builder->where('status', self::STATUS_PAUSED);
    }

    /**
     * Transfer route to another manager
     *
     * @param $manager
     *
     * @return void
     */
    public function transfer($manager)
    {
        TransferLeadOrderRoute::dispatch($this, $manager);
    }

    /**
     * Changes offer of the route
     *
     * @param \App\Offer $offer
     */
    public function changeOffer($offer)
    {
        ChangeLeadOrderRouteOffer::dispatch($this, $offer);
    }

    /**
     * Determine is particular route has custom destination
     *
     * @return bool
     */
    public function hasCustomDestination(): bool
    {
        return $this->destination_id !== null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destination()
    {
        return $this->belongsTo(LeadDestination::class);
    }

    /**
     * Create new assignment between route and lead
     *
     * @param \App\Lead $lead
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function assign(Lead $lead): LeadOrderAssignment
    {
        return AssignLeadToRoute::dispatchNow($lead, $this);
    }

    /**
     * Skips routes with managers who already have certain lead
     *
     * @param Builder   $query
     * @param Lead|null $lead
     *
     * @return Builder
     */
    public function scopeExcludeManagersWithLead(Builder $query, ?Lead $lead = null)
    {
        if ($lead !== null) {
            return $query->whereNotIn(
                'manager_id',
                LeadOrderRoute::query()
                    ->select('manager_id')
                    ->whereHas('assignments', fn ($query) => $query->whereLeadId($lead->id))
                    ->pluck('manager_id')
            );
        }

        return $query;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithOrderProgress(Builder $builder)
    {
        return $builder->addSelect([
            'order_progress' => (new LeadOrderRoute())->setTable('sub')->selectRaw(
                'COALESCE(round(sum("leadsReceived") / cast(NULLIF(sum("leadsOrdered"), 0) as decimal) * 100, 2), 0.00)'
            )
                ->from('lead_order_routes', 'sub')
                ->whereColumn('sub.order_id', 'lead_order_routes.order_id')
        ]);
    }

    /**
     * Distributes route's assignments between order managers
     *
     * @return bool
     */
    public function distribute(): bool
    {
        $managers = $this->order->getManagers($this->offer_id, $this->manager_id);

        if ($managers->count() === 0) {
            return false;
        }

        $perManager = (int) round($this->assignments->count() / $managers->count());
        foreach ($managers as $i => $manager) {
            $this->assignments->skip($i * $perManager)
                ->when($i < $managers->count() - 1, fn ($items) => $items->take($perManager))
                ->each(fn (LeadOrderAssignment $assignment) => $assignment->transfer($manager));
        }

        return true;
    }

    public function scopeWithoutAutologin(Builder $builder): Builder
    {
        $destinations = LeadDestination::query()
            ->whereIn('autologin', [LeadDestination::AUTOLOGIN_ON, LeadDestination::AUTOLOGIN_OPTIONAL])
            ->pluck('id');

        return $builder->where(function (Builder $query) use ($destinations) {
            return $query->whereNotIn('lead_order_routes.destination_id', $destinations)
                ->orWhereNull('lead_order_routes.destination_id')
                ->whereHas('order', function (Builder $q2) use ($destinations) {
                    return $q2->whereNotIn('leads_orders.destination_id', $destinations)
                        ->orWhereNull('leads_orders.destination_id')
                        ->whereHas('office', function (Builder $q3) use ($destinations) {
                            return $q3->whereNotIn('offices.destination_id', $destinations);
                        });
                });
        });
    }

    /**
     * Recalculate assignment numbers
     *
     * @return bool
     */
    public function recalculate()
    {
        $assignments = $this->assignments()->count();

        if ($this->leadsReceived > $this->leadsOrdered) {
            $this->update([
                'leadsOrdered' => $assignments,
            ]);
        }

        if ($assignments !== $this->leadsReceived) {
            return $this->update([
                'leadsReceived' => $assignments,
            ]);
        }
    }

    /**
     * Limit routes to visible
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (app()->runningInConsole() || auth()->user()->isAdmin()) {
            return $builder;
        }

        // if (auth()->user()->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
        //     return $builder->whereHas('assignments', fn ($assignmentQuery) => $assignmentQuery->visible());
        // }

        return $builder->whereIn('lead_order_routes.offer_id', auth()->user()->allowedOffers()->pluck('offers.id'));
    }

    /**
     * Get only routes with priority
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePriority(Builder $builder)
    {
        return $builder->where('lead_order_routes.priority', true);
    }

    /**
     * @param Builder     $builder
     * @param Carbon|null $date
     *
     * @return Builder
     */
    public function scopeAllowedLive(Builder $builder, ?Carbon $date = null)
    {
        return $builder->whereIn(
            'order_id',
            LeadsOrder::allowedLive()
                ->whereDate('date', ($date ?? now())->toDateString())
                ->pluck('id')
                ->values()
        );
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeSkipLiveInterval(Builder $builder)
    {
        return $builder->whereNotIn(
            'order_id',
            LeadsOrder::current()
                ->restrictedLiveInterval()
                ->pluck('id')
        );
    }
}
