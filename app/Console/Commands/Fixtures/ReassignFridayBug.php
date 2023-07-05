<?php

namespace App\Console\Commands\Fixtures;

use App\Event;
use App\Lead;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ReassignFridayBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:reassign-friday-bug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orderId = 27790;
        $routes  = [
            450386,
            450383,
            450382,
            450381,
            450385,
            450384,
            450394,
            450393,
            450395,
            450379,
            450380,
        ];

        $events = Event::whereEventableType(Lead::class)
            ->whereType(Lead::UNASSIGNED)
            ->whereDate('events.created_at', '2023-04-07')
            ->with(['eventable'])
            ->where(function ($query) use ($routes) {
                foreach ($routes as $route) {
                    $query->orWhere(
                        DB::raw('original_data::text'),
                        'like',
                        '%"route_id":' . $route . '}'
                    );
                }

                return $query;
            })->get();

        $leads           = $events->pluck('eventable')->unique('id');
        $alreadyAssigned = collect();
        $nowAssigned     = collect();
        $skippedLeads    = collect();
        $this->info('Total leads: ' . $leads->count());

        /** @var Lead $lead */
        foreach ($leads as $lead) {
            if (
                $lead->assignments()
                    ->whereDate('lead_order_assignments.created_at', '>=', '2023-04-07')
                    ->exists()
            ) {
                $alreadyAssigned->push($lead);
            } else {
                if ($this->reassignLead($lead, $routes) !== null) {
                    $nowAssigned->push($lead);
                } else {
                    $skippedLeads->push($lead);
                }
            }
        }
        $this->info(sprintf('Already assigned: %s. Ids: %s', $alreadyAssigned->count(), $alreadyAssigned->pluck('id')->join(',')));
        $this->info(sprintf('Now assigned: %s. Ids: %s', $nowAssigned->count(), $nowAssigned->pluck('id')->join(',')));
        $this->info(sprintf('Skipped leads: %s. Ids: %s', $skippedLeads->count(), $skippedLeads->pluck('id')->join(',')));

        return 0;
    }

    /**
     * @param Lead  $lead
     * @param array $routes
     *
     * @return LeadOrderAssignment|null
     */
    protected function reassignLead(Lead $lead, array $routes): ?LeadOrderAssignment
    {
        $deletedAssEvent = Event::whereEventableType(LeadOrderAssignment::class)
            ->whereType('deleted')
            ->whereDate('events.created_at', '2023-04-07')
            ->where(
                DB::raw('original_data::text'),
                'like',
                '%"lead_id":' . $lead->id . ',%'
            )
            ->orderByDesc('created_at')
            ->first();

        if ($deletedAssEvent === null) {
            $this->warn(sprintf('Skip lead[%s], no deleted ass event found for lead.', $lead->id));

            return null;
        }
        if (!in_array($deletedAssEvent->original_data['route_id'], $routes)) {
            $this->warn(sprintf(
                'Skip lead[%s], deleted ass event\'s route is different: %s',
                $lead->id,
                $deletedAssEvent->original_data['route_id']
            ));

            return null;
        }

        $assignment = LeadOrderAssignment::create(
            Arr::except($deletedAssEvent->original_data, ['id', 'deliver_at', 'created_at', 'updated_at'])
        );

        $lead->addEvent(
            Lead::ASSIGNED,
            [
                'manager_id'     => $assignment->route->manager_id,
                'office_id'      => $assignment->route->order->office_id,
                'order_id'       => $assignment->route->order_id,
                'offer_id'       => $assignment->route->offer_id,
                'destination_id' => $assignment->destination_id,
                'queue'          => true,
                'reassigned'     => true,
            ]
        );

        $assignment->route->increment('leadsReceived');

        return $assignment;
    }
}
