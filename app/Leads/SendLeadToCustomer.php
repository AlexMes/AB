<?php

namespace App\Leads;

use App\Jobs\DeliverAssignment;
use App\Lead;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Leads\Pipes\EnsureLeadIsNotAssigned;
use App\Leads\Pipes\EnsureLeadIsValid;
use App\Leads\Pipes\EnsureLeadLockAcquired;
use App\LeadsOrder;
use App\Office;
use App\Trail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\SerializesModels;

class SendLeadToCustomer
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Lead to assign
     *
     * @var \App\Lead
     */
    protected Lead $lead;

    /**
     * Routes to skip
     *
     * @var array
     */
    protected $skip = [];

    /**
     * Marker for retries
     *
     * @var bool
     */
    protected bool $retry = true;

    /**
     * Set of checks to run before
     * assigning lead to route
     *
     * @var array|string[]
     */
    protected array $checks = [
        EnsureLeadIsValid::class,
        EnsureLeadIsNotAssigned::class,
        EnsureLeadLockAcquired::class,
    ];

    /**
     * SendLeadToCustomer constructor.
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Create assignment and send lead to customer
     *
     * @throws \Throwable
     *
     * @return \App\LeadOrderAssignment|null
     */
    public function handle(): ?LeadOrderAssignment
    {
        if ($this->leadCanBeAssigned() === false) {
            return null;
        }

        $tries = 1;

        do {
            app(Trail::class)->add('Attempt '. $tries.' Routes to skip: '. collect($this->skip)->implode(','));

            $route = DetermineRoute::forLead($this->lead)->skipRoutes($this->skip)->get();

            if ($route === null) {
                app(Trail::class)->add('No routes available for lead, offer '.$this->lead->offer->name);
                app(Trail::class)->add(sprintf(
                    'Route stats for offer %s. Active cap: %s. Paused cap: %s. Offices with active cap: %s',
                    str_replace('_', '-', $this->lead->offer->name),
                    LeadOrderRoute::current()->where('offer_id', $this->lead->offer_id)->active()->selectRaw('SUM("leadsOrdered") - SUM("leadsReceived") as capacity')->value('capacity') ?? 0,
                    LeadOrderRoute::current()->where('offer_id', $this->lead->offer_id)->paused()->selectRaw('SUM("leadsOrdered") - SUM("leadsReceived") as capacity')->value('capacity') ?? 0,
                    Office::whereIn(
                        'offices.id',
                        LeadsOrder::current()
                            ->whereHas('routes', fn ($query) => $query->active()->incomplete()->where('offer_id', $this->lead->offer_id))->pluck('office_id')
                    )->pluck('offices.name')->implode(',') ?? 'none'
                ));


                return null;
            }

            try {
                $assignment = DeliverAssignment::dispatchNow(AssignLeadToRoute::dispatchNow($this->lead, $route));
            } catch (\Throwable $th) {
                app(Trail::class)->add('Exception: '.$th->getMessage());

                $this->skip[] = $route->id;

                $tries++;

                return null;
            }

            if ($assignment->isConfirmed() || in_array(optional($assignment->destination)->driver, [LeadDestinationDriver::GSHEETS, LeadDestinationDriver::THRBUYERS])) {
                $this->retry = false;

                return $assignment;
            }

            $tries++;

            $this->skip[] = $route->id;

            optional($assignment)->remove();
        } while ($this->retry);

        return null;
    }

    /**
     * Determines is lead can be assigned
     *
     * @return bool
     */
    protected function leadCanBeAssigned(): bool
    {
        return app(Pipeline::class)
            ->send($this->lead)
            ->through($this->checks)
            ->thenReturn() !== null;
    }
}
