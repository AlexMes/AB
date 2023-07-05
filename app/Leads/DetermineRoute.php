<?php

namespace App\Leads;

use App\Lead;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Trail;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DetermineRoute
{
    /**
     * @var \App\Lead
     */
    protected Lead $lead;

    /**
     * @var bool
     */
    protected bool $ownLanding;

    /**
     * @var array
     */
    protected array $routesToSkip = [];

    /**
     * @var array|int[]
     */
    protected array $timezoneOffices = [];

    /**
     * DetermineRoute constructor.
     *
     * @param \App\Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead       = $lead->load('offer');
        $this->ownLanding = !Str::startsWith($lead->form_type, 'integration-');
    }

    /**
     * Get route for assigner
     *
     * @return \App\LeadOrderRoute|null
     */
    public function get(): ?LeadOrderRoute
    {
        $priorityRoute = $this->getPriorityRoute();

        if ($priorityRoute !== null) {
            app(Trail::class)->add('Got priority route for assignment.');

            return $priorityRoute;
        }

        return LeadOrderRoute::current()
            ->allowedLive()
            ->skipLiveInterval()
            ->incomplete()
            ->active()
            ->withOrderProgress()
            ->excludeOffices(array_merge([], $this->skipOnCondition()))
            ->whereOfferId($this->lead->offer_id)
            /*->excludeOfficesWithCompletedPayments()*/
            ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
            ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
            ->unless($this->ownLanding, fn ($query) => $query->withoutAutologin())
            ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
            ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
            ->first();
    }

    /**
     * Get matching route for lead. Shortcut
     *
     * @param \App\Lead $lead
     *
     * @return \App\Leads\DetermineRoute
     */
    public static function forLead(Lead $lead): DetermineRoute
    {
        return new static($lead);
    }

    /**
     * Get routes with prioritized settings.
     * Now used only for night assignments
     *
     * @return \App\LeadOrderRoute|null
     */
    protected function getPriorityRoute(): ?LeadOrderRoute
    {
        $this->lead->load(['ipAddress','user','user.branch']);

        $route = null;

        $route = LeadOrderRoute::current()
            ->allowedLive()
            ->skipLiveInterval()
            ->incomplete()
            ->active()
            ->priority()
            ->withOrderProgress()
            ->excludeOffices(array_merge([], $this->skipOnCondition()))
            ->whereOfferId($this->lead->offer_id)
            /*->excludeOfficesWithCompletedPayments()*/
            ->excludeCountryCondition(optional($this->lead->ipAddress)->country_code, $this->lead->offer_id)
            ->excludeMarkersCondition($this->lead->markers->pluck('name')->toArray())
            ->unless($this->ownLanding, fn ($query) => $query->withoutAutologin())
            ->when($this->routesToSkip, fn ($query) => $query->whereNotIn('id', $this->routesToSkip))
            ->orderByRaw('order_progress, progress, last_received_at ASC NULLS FIRST')
            ->first();

        if ($route !== null) {
            app(Trail::class)->add('Route confirgured as priority');

            return $route;
        }

        return null;
    }

    protected function canBePrioritizedByTimezone(): bool
    {
        if (optional($this->lead->ipAddress)->timezone === null) {
            return false;
        }

        $moscow = now('Europe/Moscow');
        $diff   = (now($this->lead->ipAddress->timezone)->utcOffset() - $moscow->utcOffset()) / 60;

        return $diff <= 8 && $moscow->greaterThanOrEqualTo($moscow->clone()->setTime(9, 0))
            && $moscow->lessThan($moscow->clone()->setTime(14, 0))
            || $diff <= 4 && $moscow->greaterThanOrEqualTo($moscow->clone()->setTime(14, 0))
            && $moscow->lessThan($moscow->clone()->setTime(17, 0))
            || $diff <= 3 && $moscow->greaterThanOrEqualTo($moscow->clone()->setTime(17, 0))
            && $moscow->lessThan($moscow->clone()->setTime(19, 0))
            || $diff <= 2 && $moscow->greaterThanOrEqualTo($moscow->clone()->setTime(19, 0));
    }

    /**
     * Get offices to skip given some conditions
     *
     * @return array
     */
    protected function skipOnCondition(): array
    {
        $noDuplicatesOffices = LeadsOrder::query()
            ->whereIn('office_id', [224, 419])
            ->whereHas('assignments.lead', fn ($query) => $query->where('phone', $this->lead->phone))
            ->where('date', '>=', now()->subDays(60))
            ->pluck('leads_orders.office_id');

        return collect($this->timezoneOffices)
            ->when(now()->hour < 4 && optional($this->lead->user)->branch_id !== 19 && in_array(optional($this->lead->ipAddress)->utc_offset, ['+0100','+0300','+0200','+0400','+0500']), function (Collection $collection) {
                // Skip msk for armani
                app(Trail::class)->add('Skipping MSK tz for night offices.');

                return $collection->merge([60,68,71]);
            })
            ->when(optional($this->lead->ipAddress)->country_code === 'EE', function (Collection $collection) {
                app(Trail::class)->add('Ignore EE for Histmo');

                return $collection->merge([174]);
            }) ->when(optional($this->lead->ipAddress)->country_code === 'BE', function (Collection $collection) {
                app(Trail::class)->add('Ignore BE for Dandy');

                return $collection->merge([90]);
            })->when(in_array(optional($this->lead->ipAddress)->country_code, ['DE','GB']), function (Collection $collection) {
                app(Trail::class)->add('Ignore DE for Chicagos');

                return $collection->merge([175,177]);
            })->merge($noDuplicatesOffices)->toArray() ?? [];
    }

    /**
     * @param array $routes
     *
     * @return $this
     */
    public function skipRoutes(array $routes = []): DetermineRoute
    {
        $this->routesToSkip = $routes;

        return $this;
    }
}
