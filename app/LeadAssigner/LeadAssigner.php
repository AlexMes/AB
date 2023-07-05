<?php

namespace App\LeadAssigner;

use App\DistributionRule;
use App\Jobs\DeliverAssignment;
use App\Jobs\SimulateAutologin;
use App\Lead;
use App\LeadAssigner\Checks\EnsureLeadHaveOffer;
use App\LeadAssigner\Checks\EnsureLeadIsValid;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Leads\Pipes\EnsureLeadIsNotAssigned;
use App\Leads\Pipes\EnsureLeadLockAcquired;
use App\LeadsOrder;
use App\Offer;
use App\Office;
use App\Trail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LeadAssigner implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use InteractsWithQueue;

    public const RESELL = 'resell';

    public const RESELL_OFFER = 'resell-offer';

    /**
     * @var array
     */
    public array $leadAttributes;

    /**
     * @var \App\Lead
     */
    public Lead $lead;

    /**
     * Pipes for a lead
     *
     * @var array
     */
    protected $pipes = [
        EnsureLeadIsValid::class,
        EnsureLeadHaveOffer::class,
        EnsureLeadIsNotAssigned::class,
        EnsureLeadLockAcquired::class,
    ];

    /**
     * @var array
     */
    protected $except;

    /**
     * @var array
     */
    protected $only;

    /**
     * @var Carbon|null
     */
    public ?Carbon $registeredAt = null;

    /**
     * @var null
     */
    protected ?string $mode = null;

    /**
     * @var bool
     */
    protected bool $simulateAutologin = false;

    /**
     * @var Carbon|null
     */
    protected ?Carbon $deliverAt = null;

    /**
     * @var bool
     */
    protected bool $retry = false;

    /**
     * @var false
     */
    protected bool $ignorePaused = false;

    /**
     * @var null
     */
    protected $replaceAuthId;

    /**
     * @var LeadOrderRoute
     */
    protected ?LeadOrderRoute $route = null;

    /**
     * @var Carbon|null
     */
    protected ?Carbon $deliverSince = null;

    /**
     * @var null
     */
    protected ?string $smoothSort = null;

    /**
     * @var array
     */
    protected array $routesToSkip = [];

    /**
     * @var bool
     */
    protected bool $freshLead = false;

    /**
     * LeadAssigner constructor.
     *
     * @param \App\Lead                       $lead
     * @param null                            $except
     * @param null                            $only
     * @param \Illuminate\Support\Carbon|null $registeredAt
     * @param null                            $mode
     * @param mixed                           $simulateAutologin
     * @param Carbon|null                     $deliverAt
     * @param bool                            $retry
     * @param bool                            $ignorePaused
     * @param null|mixed                      $replaceAuthId
     * @param LeadOrderRoute|null             $route
     * @param Carbon|null                     $deliverSince
     * @param string|null                     $smoothSort
     */
    public function __construct(
        Lead $lead,
        $except = null,
        $only = null,
        Carbon $registeredAt = null,
        $mode = null,
        $simulateAutologin = false,
        Carbon $deliverAt = null,
        $retry = false,
        $ignorePaused = false,
        $replaceAuthId = null,
        LeadOrderRoute $route = null,
        Carbon $deliverSince = null,
        ?string $smoothSort = null
    ) {
        $this->leadAttributes    = $lead->attributesToArray();
        $this->except            = empty($except) ? null : $except;
        $this->only              = empty($only) ? null : $only;
        $this->registeredAt      = $registeredAt;
        $this->mode              = $mode;
        $this->simulateAutologin = $simulateAutologin;
        $this->deliverAt         = $deliverAt;
        $this->retry             = $retry;
        $this->ignorePaused      = $ignorePaused;
        $this->replaceAuthId     = $replaceAuthId;
        $this->route             = $route;
        $this->deliverSince      = $deliverSince;
        $this->smoothSort        = $smoothSort;
    }

    /**
     * Assign lead to manager and send it to corresponding
     * spreadsheet
     *
     * @return void
     */
    public function handle()
    {
        $this->lead      = (new Lead($this->leadAttributes))->load('offer', 'ipAddress');
        $offer           = optional($this->lead->offer)->getOriginalDuplicateCopy();
        $this->freshLead = $this->lead->created_at->clone()
            ->endOfDay()->diffInDays(($this->deliverSince ?? now())->clone()->endOfDay(), false) < 1;
        app(Trail::class)->add('Except is ' . json_encode($this->except ?? []));
        app(Trail::class)->add('Only is ' . json_encode($this->only ?? []));
        app(Trail::class)->add('Mode is ' . $this->mode);
        app(Trail::class)->add('Simulation is ' . $this->simulateAutologin);

        app(Trail::class)->add(sprintf('Assigning leftover lead %s. Geo is %s %s', $this->lead->id, optional($this->lead->ipAddress)->country_code, optional($this->lead->lookup)->country));

        if ($this->only) {
            $rules = DistributionRule::whereIn('office_id', $this->only)->where('offer_id', optional($offer)->id)->get(['id','geo','allowed'])
                ->map(fn (DistributionRule $rule) => sprintf('**%s** -> __%s__', $rule->geo, $rule->allowance))->implode(' / ');
            if ($rules) {
                app(Trail::class)->add('Distribution rules ' . $rules);
            }
        }

        app(Pipeline::class)
            ->send($this->lead)
            ->through($this->pipes)
            ->then(function (Lead $lead) {
                if ($lead->offer_id === null) {
                    app(Trail::class)->add('Lead without offer, quit');

                    return;
                }
                $offer = $this->route !== null ? $this->route->offer : $lead->offer->getOriginalDuplicateCopy();

                do {
                    $route = $this->route ?? $this->route($offer->id);

                    if ($route !== null) {
                        if (!$route->lock() && $this->deliverAt === null) {
                            // Kind of retry in 2 seconds if lock aren't obtained
                            app(Trail::class)->add('Route locked, retry in 2 seconds');

                            return $this->release(2);
                        }
                        $assignment = LeadOrderAssignment::create([
                            'lead_id'            => $lead->id,
                            'route_id'           => $route->id,
                            'registered_at'      => $this->registeredAt ?? $lead->created_at,
                            'is_live'            => false,
                            'deliver_at'         => $this->deliverAt,
                            'simulate_autologin' => $this->simulateAutologin,
                            'replace_auth_id'    => $this->replaceAuthId,
                            'smooth_sort'        => $this->smoothSort,
                        ]);
                        $assignment->lead->offer_id = $route->offer_id;

                        $this->lead->addEvent(
                            Lead::ASSIGNED,
                            [
                                'manager_id'     => $route->manager_id,
                                'office_id'      => $route->order->office_id,
                                'order_id'       => $route->order_id,
                                'offer_id'       => $route->offer_id,
                                'destination_id' => $assignment->destination_id,
                                'queue'          => true
                            ]
                        );

                        $route->increment('leadsReceived');

                        if ($this->deliverAt === null) {
                            try {
                                DeliverAssignment::dispatchNow($assignment);
                            } catch (\Throwable $throwable) {
                            }

                            if ($this->simulateAutologin) {
                                app(Trail::class)->add('Simulating autologin');
                                SimulateAutologin::dispatch($assignment)->onQueue('autologin');
                            }

                            if ($this->retry) {
                                $assignment->refresh();

                                if ($assignment->isConfirmed() || in_array(optional($assignment->destination)->driver, [LeadDestinationDriver::GSHEETS, LeadDestinationDriver::THRBUYERS])) {
                                    $this->retry = false;
                                } else {
                                    $this->routesToSkip[] = $route->id;

                                    $assignment->remove();
                                }
                            }
                        } else {
                            \Log::channel('smooth-lo')->debug(sprintf(
                                'Assigned at:%s, assignment:%s, deliver_at(plan):%s, deliver_at(ass):%s',
                                now()->toDateTimeString(),
                                $assignment->id,
                                $this->deliverAt->toDateTimeString(),
                                optional($assignment->deliver_at)->toDateTimeString()
                            ));
                        }
                    }
                } while ($this->retry && $route !== null && $this->route === null);

                if ($route === null && $offer !== null) {
                    try {
                        app(Trail::class)->add('No routes available for lead ' . $lead->id . ', offer ' . str_replace('_', '-', $offer->name));
                        app(Trail::class)->add(sprintf(
                            'Route stats for offer %s. Active cap: %s. Paused cap: %s. Offices with active cap: %s',
                            str_replace('_', '-', $offer->name),
                            LeadOrderRoute::forDate($this->deliverSince)->where('offer_id', $offer->id)->active()->selectRaw('SUM("leadsOrdered") - SUM("leadsReceived") as capacity')->value('capacity') ?? 0,
                            LeadOrderRoute::forDate($this->deliverSince)->where('offer_id', $offer->id)->paused()->selectRaw('SUM("leadsOrdered") - SUM("leadsReceived") as capacity')->value('capacity') ?? 0,
                            Office::whereIn(
                                'offices.id',
                                LeadsOrder::forDate($this->deliverSince)
                                    ->whereHas('routes', fn ($query) => $query->active()->incomplete()->where('offer_id', $offer->id))->pluck('office_id')
                            )->pluck('offices.name')->implode(',') ?? 'none'
                        ));
                    } catch (\Throwable $th) {
                        app(Trail::class)->add('Cant figure offer for ' . $lead->id . ' Domain is ' . $lead->domain . ' Affiliate is ' . $lead->affiliate_id);
                    }
                }
            });

        if (app()->runningInConsole()) {
            app(Trail::class)->send();
        }
    }

    /**
     * Determine who should receive lead
     *
     * @param int $offerId
     *
     * @return \App\LeadOrderRoute|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function route(int $offerId)
    {
        if ($this->only === null) {
            $priorityRoute = $this->priorityRoute($offerId);

            if ($priorityRoute !== null) {
                app(Trail::class)->add('Using priority route');

                return $priorityRoute;
            }
        }

        app(Trail::class)->add('Only isset');

        $of = $this->mode === static::RESELL_OFFER ? Offer::find($offerId)->getResellCopy()->id : $offerId;

        app(Trail::class)->add($of);

        return LeadOrderRoute::forDate($this->deliverSince)
            ->incomplete()
            ->unless($this->ignorePaused, fn ($query) => $query->active())
            ->whereOfferId($of)
            ->excludeOffices(array_merge($this->except() ?? [], [23]))
            ->onlyOffices($this->only ?? [])
            // ->excludeManagersWithLead($this->lead)
            ->withOrderProgress()
            /*->excludeOfficesWithCompletedPayments()*/
            // ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
            ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
            ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
            ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
            ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
            ->first();
    }

    public function except()
    {
        $noDuplicatesOffices = LeadsOrder::query()
            ->whereIn('office_id', [224, 419])
            ->whereHas('assignments.lead', fn ($query) => $query->where('phone', $this->lead->phone))
            ->where('date', '>=', now()->subDays(60))
            ->pluck('leads_orders.office_id');

        return collect([])
            ->when(
                in_array(optional($this->lead->ipAddress)->country_code, ['DE','GB']),
                fn (Collection $collection) => $collection->merge([174])
            )
            ->merge($noDuplicatesOffices)
            ->toArray() ?? [];

        // return collect($this->except)
        // ->when(optional($this->lead->user)->branch_id === 16 && in_array(optional($this->lead->ipAddress)->utc_offset, ['+0100','+0300','+0200','+0400','+0500','+0600']), function (Collection $collection) {
            //     // Skip timezones for jnik
            //     return $collection->merge([68]);
        // })
        // ->when(optional($this->lead->user)->branch_id !== 19 && in_array(optional($this->lead->ipAddress)->utc_offset, ['+0100','+0300','+0200']), function (Collection $collection) {
            //     // Skip msk for armani
            //     return $collection->merge([60,68]);
        // })
        // ->when($this->lead->hasMarker('caucasian') && !optional($this->lead->offer)->isLeftover(), function (Collection $collection) {
            //     if (optional($this->lead->user)->branch_id === 19) {
            //         return $collection->merge(Office::whereNotIn('id', [6,98,16,54,66,41,48,89,128,129,132,133])->pluck('id')->values());
            //     }
            //     if (optional($this->lead->user)->branch_id === 16) {
            //         return $collection->merge([1]);
            //     }

            //     return $collection->merge(Office::whereNotIn('id', [6,98,51,16,54,36,66,41,48,89,128,129,132,133])->pluck('id')->values());
        // })->toArray() ?? [];
    }

    /**
     * @param int $offerId
     *
     * @return \App\LeadOrderRoute|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    protected function priorityRoute(int $offerId)
    {
        $this->lead->load('ipAddress');

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['HR'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([81,103])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['ES'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([87,88,81,103])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['DE','GR'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([87,88,41,81,103])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['CZ'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([87,88,41])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['AT','DK','IE','FI','CH','NL','NO','PT','SI','SK'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([87,41,81,103])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['FR'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([87,88])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['BE','HR','LU','SE'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([87,41])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($this->lead->offer_id, [579,578]) && in_array(optional($this->lead->ipAddress)->country_code, ['EE','IT','LT','HU','GB','LV'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices([87,81,103])
                ->whereOfferId($this->lead->offer_id)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($offerId, [483,558]) && in_array(optional($this->lead->ipAddress)->country_code, ['NL','HR','SE','SI','CZ','BE','DK'])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->onlyOffices(65)
                ->whereOfferId($offerId)
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($offerId, [167,175,208,204,202,191,229,216,254,253,250,218,224])) {
            return null;
        }

        $route = null;

        if (in_array($this->lead->offer_id, [817])) {
            $route = LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->whereOfferId(652)
                ->excludeOffices(array_merge($this->except() ?? [], []))
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if ($route !== null) {
            app(Trail::class)->add('Night offer dv 652 (dv-5).' . $route->id);

            return $route;
        }

        if (in_array($offerId, [146]) && $route === null) {
            $route = LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->whereOfferId(439)
                ->excludeOffices(array_merge($this->except() ?? [], []))
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if (in_array($offerId, [967])) {
            $route = LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->whereOfferId(391)
                ->excludeOffices(array_merge($this->except() ?? [], []))
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        if ($route !== null) {
            return $route;
        }

        if (in_array($this->lead->offer_id, [812])) {
            return LeadOrderRoute::forDate($this->deliverSince)
                ->incomplete()
                ->unless($this->ignorePaused, fn ($query) => $query->active())
                ->whereOfferId(40)
                ->excludeOffices(array_merge($this->except() ?? [], []))
                ->excludeManagersWithLead($this->lead)
                /*->excludeOfficesWithCompletedPayments()*/
                ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
                ->withOrderProgress()
                ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
                ->when($this->freshLead, fn ($query) => $query->allowedLive($this->deliverSince))
                ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
                ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
                ->first();
        }

        return null;
    }
    /**
     * Retry sending for an hour
     *
     * @return \Illuminate\Support\Carbon
     */
    public function retryUntil()
    {
        return now()->addHour();
    }
}
