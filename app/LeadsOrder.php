<?php

namespace App;

use App\Events\LeadOrderCreated;
use App\Events\LeadOrderUpdated;
use App\Jobs\CloneLeadOrder;
use App\Traits\RecordEvents;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\LeadsOrder
 *
 * @property int $id
 * @property \datetime $date
 * @property int $office_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $destination_id
 * @property string|null $start_at
 * @property string|null $stop_at
 * @property int $delayed_assignments
 * @property int|null $branch_id
 * @property bool $autodelete_duplicates
 * @property bool $deny_live
 * @property int $live_interval
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderAssignment[] $assignments
 * @property-read int|null $assignments_count
 * @property-read \App\Branch|null $branch
 * @property-read \App\LeadDestination|null $destination
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read int $active_routes_count
 * @property-read mixed $deliver_confirmed_count
 * @property-read int $deliver_count
 * @property-read float $delivery_percent
 * @property-read int $finished_routes_count
 * @property-read void $leads_ordered
 * @property-read void $leads_received
 * @property-read int $paused_routes_count
 * @property-read \Illuminate\Database\Eloquent\Collection $progress
 * @property-read \App\Office $office
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderRoute[] $routes
 * @property-read int|null $routes_count
 *
 * @method static Builder|LeadsOrder allowedLive()
 * @method static Builder|LeadsOrder current()
 * @method static Builder|LeadsOrder newModelQuery()
 * @method static Builder|LeadsOrder newQuery()
 * @method static Builder|LeadsOrder query()
 * @method static Builder|LeadsOrder restrictedLiveInterval()
 * @method static Builder|LeadsOrder visible()
 * @method static Builder|LeadsOrder whereAutodeleteDuplicates($value)
 * @method static Builder|LeadsOrder whereBranchId($value)
 * @method static Builder|LeadsOrder whereCreatedAt($value)
 * @method static Builder|LeadsOrder whereDate($value)
 * @method static Builder|LeadsOrder whereDelayedAssignments($value)
 * @method static Builder|LeadsOrder whereDeletedAt($value)
 * @method static Builder|LeadsOrder whereDenyLive($value)
 * @method static Builder|LeadsOrder whereDestinationId($value)
 * @method static Builder|LeadsOrder whereId($value)
 * @method static Builder|LeadsOrder whereLiveInterval($value)
 * @method static Builder|LeadsOrder whereOfficeId($value)
 * @method static Builder|LeadsOrder whereStartAt($value)
 * @method static Builder|LeadsOrder whereStopAt($value)
 * @method static Builder|LeadsOrder whereUpdatedAt($value)
 * @method static Builder|LeadsOrder withActiveRoutesCount()
 * @method static Builder|LeadsOrder withDeliverAtAssignmentCount()
 * @method static Builder|LeadsOrder withDeliveryPercent()
 * @method static Builder|LeadsOrder withFinishedRoutesCount()
 * @method static Builder|LeadsOrder withLastReceived()
 * @method static Builder|LeadsOrder withMissedDeliverAtAssignmentCount(?\Illuminate\Support\Carbon $date = null)
 * @method static Builder|LeadsOrder withOrdered()
 * @method static Builder|LeadsOrder withPausedRoutesCount()
 * @method static Builder|LeadsOrder withReceived()
 * @mixin \Eloquent
 */
class LeadsOrder extends Model
{
    use RecordEvents;

    /**
     * Table name in DB
     *
     * @var string
     */
    protected $table = 'leads_orders';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d'
    ];

    /**
     * Default amount of items on page
     *
     * @var int
     */
    protected $perPage = 50;

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => LeadOrderCreated::class,
        'updated' => LeadOrderUpdated::class,
    ];

    /**
     * Related office, which should reecive leads
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routes()
    {
        return $this->hasMany(LeadOrderRoute::class, 'order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeCurrent(Builder $builder)
    {
        return $builder->whereDate('date', now()->toDateString());
    }

    /**
     * Limit orders to visible only
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isAdmin()) {
            return $builder;
        }

        if (auth()->id() === 230) {
            return $builder->where('date', '<', '2021-11-05');
        }

        if (auth()->check() && auth()->user()->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            return $builder
                ->where(function ($query) {
                    return $query->whereHas('routes', function ($routeQuery) {
                        $routeQuery->visible();
                    })->orWhereDoesntHave('routes');
                })
                ->where(function ($query) {
                    return $query->where('leads_orders.branch_id', auth()->user()->branch_id)
                        ->orWhereNull('leads_orders.branch_id')
                        ->whereIn('leads_orders.office_id', auth()->user()->branch->offices()->pluck('offices.id'));
                });
            /*->whereDate('date', '>=', now()->startOfYear()->toDateString());*/
        }

        return $builder;
    }

    /**
     * Get amount of ordered leads
     *
     * @return void
     */
    public function getLeadsOrderedAttribute()
    {
        return $this->routes->sum('leadsOrdered');
    }

    /**
     * Get amount of received leads
     *
     * @return void
     */
    public function getLeadsReceivedAttribute()
    {
        return $this->routes->sum('leadsReceived');
    }

    /**
     * Select amount of received leads
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeWithReceived(Builder $builder)
    {
        $builder->addSelect([
            'received' => LeadOrderRoute::visible()->selectRaw('coalesce(sum("leadsReceived"),0)')->whereColumn('lead_order_routes.order_id', '=', 'leads_orders.id'),
        ]);
    }

    /**
     * Select amount of ordered leads
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeWithOrdered(Builder $builder)
    {
        $builder->addSelect([
            'ordered' => LeadOrderRoute::visible()->selectRaw('coalesce(sum("leadsOrdered"),0)')->whereColumn('lead_order_routes.order_id', '=', 'leads_orders.id'),
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeWithLastReceived(Builder $builder)
    {
        $builder->addSelect([
            'lastReceivedAt' => LeadOrderRoute::visible()->select('last_received_at')
                ->whereColumn('lead_order_routes.order_id', '=', 'leads_orders.id')
                ->whereNotNull('last_received_at')
                ->latest('last_received_at')
                ->limit(1),
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProgressAttribute()
    {
        return $this->routes()->visible()->select([
            DB::raw('offers.name as name'),
            DB::raw('coalesce(sum("leadsReceived"),0) as received'),
            DB::raw('coalesce(sum("leadsOrdered"),0) as ordered'),
        ])->join('offers', 'lead_order_routes.offer_id', '=', 'offers.id')
            ->groupBy('name')
            ->get();
    }

    /**
     * Determines is order for today
     *
     * @return bool
     */
    public function isCurrent()
    {
        return $this->date->isCurrentDay();
    }

    /**
     * Clone leads order to a new date
     *
     * @param \Carbon\CarbonInterface $date
     *
     * @throws \Throwable
     */
    public function cloneToDate(CarbonInterface $date)
    {
        CloneLeadOrder::dispatchNow($this, $date);
    }

    /**
     * Determine is particular order has custom destination
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
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithActiveRoutesCount(Builder $builder)
    {
        return $builder->addSelect([
            'activeRoutesCount' => LeadOrderRoute::visible()->selectRaw('count(*)')
                ->whereColumn('order_id', '=', 'leads_orders.id')
                ->where('status', LeadOrderRoute::STATUS_ACTIVE),
        ]);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithPausedRoutesCount(Builder $builder)
    {
        return $builder->addSelect([
            'pausedRoutesCount' => LeadOrderRoute::visible()->selectRaw('count(*) ')
                ->whereColumn('order_id', '=', 'leads_orders.id')
                ->where('status', LeadOrderRoute::STATUS_PAUSED),
        ]);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithFinishedRoutesCount(Builder $builder)
    {
        return $builder->addSelect([
            'finishedRoutesCount' => LeadOrderRoute::visible()->selectRaw('count(*)')
                ->whereColumn('lead_order_routes.order_id', '=', 'leads_orders.id')
                ->whereColumn('leadsReceived', '=', 'leadsOrdered')
                ->limit(1),
        ]);
    }

    /**
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return Builder
     */
    public function scopeWithDeliveryPercent(Builder $builder)
    {
        return $builder->addSelect([
            'deliveryPercent' => LeadOrderAssignment::visible()
                ->selectRaw('CASE WHEN count(CASE WHEN deliver_at is null OR confirmed_at is not null OR delivery_failed is not null THEN 1 END) > 0 THEN round(count(CASE WHEN confirmed_at is not null THEN 1 END)::decimal / count(CASE WHEN deliver_at is null OR confirmed_at is not null OR delivery_failed is not null THEN 1 END) * 100, 2) ELSE 0 END')
                ->whereIn(
                    'route_id',
                    LeadOrderRoute::visible()->select('id')->whereColumn('lead_order_routes.order_id', 'leads_orders.id')
                ),
        ]);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithDeliverAtAssignmentCount(Builder $builder)
    {
        return $builder->addSelect([
            'deliverCount' => LeadOrderAssignment::visible()
                ->selectRaw('count(CASE WHEN deliver_at is not null THEN 1 END)')
                ->whereIn(
                    'route_id',
                    LeadOrderRoute::visible()->select('id')->whereColumn('lead_order_routes.order_id', 'leads_orders.id')
                ),
        ]);
    }

    /**
     * @param Builder     $builder
     * @param Carbon|null $date
     *
     * @return Builder
     */
    public function scopeWithMissedDeliverAtAssignmentCount(Builder $builder, ?Carbon $date = null)
    {
        $date = optional($date)->toImmutable();

        return $builder->addSelect([
            'missedDeliverCount' => LeadOrderAssignment::visible()
                ->selectRaw('count(*)')
                ->whereNull('confirmed_at')
                ->where('deliver_at', '<=', ($date ?? now())->seconds(0)->toDateTimeString())
                ->whereIn(
                    'route_id',
                    LeadOrderRoute::visible()->select('id')->whereColumn('lead_order_routes.order_id', 'leads_orders.id')
                ),
        ]);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedLive(Builder $builder)
    {
        return $builder->whereDenyLive(false);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeRestrictedLiveInterval(Builder $builder)
    {
        return $builder->where('live_interval', '>', 0)
            ->whereHas('routes', function ($query) {
                return $query->whereNotNull('last_received_at')
                    ->where(\DB::raw("last_received_at + concat(live_interval, ' seconds')::interval"), '>', now());
            });
    }

    /**
     * @return int
     */
    public function getActiveRoutesCountAttribute()
    {
        return $this->routes()->visible()->where('status', LeadOrderRoute::STATUS_ACTIVE)->count();
    }

    /**
     * @return int
     */
    public function getPausedRoutesCountAttribute()
    {
        return $this->routes()->visible()->where('status', LeadOrderRoute::STATUS_PAUSED)->count();
    }

    /**
     * @return int
     */
    public function getFinishedRoutesCountAttribute()
    {
        return $this->routes()->visible()->whereColumn('leadsReceived', '=', 'leadsOrdered')->count();
    }

    /**
     * @return float
     */
    public function getDeliveryPercentAttribute()
    {
        $assignments = $this->routes->flatMap->assignments;

        $cnt = $assignments
            ->filter(function (LeadOrderAssignment $assignment) {
                return is_null($assignment->deliver_at)
                    || !is_null($assignment->confirmed_at) || !is_null($assignment->delivery_failed);
            })
            ->count();

        return $cnt > 0
            ? round($assignments->whereNotNull('confirmed_at')->count() / $cnt * 100, 2)
            : 0;
    }

    /**
     * @return int
     */
    public function getDeliverCountAttribute()
    {
        return LeadOrderAssignment::visible()
            ->whereIn(
                'route_id',
                $this->routes
                    ->pluck('id'),
            )
            ->whereNotNull('deliver_at')
            ->count();
    }

    public function getDeliverConfirmedCountAttribute()
    {
        return LeadOrderAssignment::visible()
            ->whereIn(
                'route_id',
                $this->routes
                    ->pluck('id'),
            )
            ->whereNotNull('deliver_at')
            ->whereNotNull('confirmed_at')
            ->count();
    }

    public function assignments()
    {
        return $this->hasManyThrough(
            LeadOrderAssignment::class,
            LeadOrderRoute::class,
            'order_id',
            'route_id'
        );
    }

    /**
     * @param array|int|null $offers
     * @param array|int|null $exceptManagers
     *
     * @return \Illuminate\Support\Collection
     */
    public function getManagers($offers = null, $exceptManagers = null): \Illuminate\Support\Collection
    {
        return Manager::query()
            ->whereIn(
                'id',
                $this->routes()
                    ->select('manager_id')
                    ->when(
                        $exceptManagers,
                        fn (Builder $query) => $query->whereNotIn('manager_id', Arr::wrap($exceptManagers))
                    )
                    ->when($offers, fn (Builder $query) => $query->whereIn('offer_id', Arr::wrap($offers)))
                    ->pluck('manager_id')
            )->get();
    }
}
