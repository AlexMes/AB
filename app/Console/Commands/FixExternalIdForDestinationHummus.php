<?php

namespace App\Console\Commands;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FixExternalIdForDestinationHummus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:hummus-fix-external-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change external_id in table lead_order_assignments for driver hummus';

    /*
     * Date creation driver Hummus
     *
     * @var string
     */
    protected string $dateCreatedDriver = '2022-09-23';
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::HUMMUS)->get();

        foreach ($destinations as $destination) {
            $counter = 0;
            if (
                LeadOrderAssignment::where('destination_id', $destination->id)
                    ->where('created_at', '>=', Carbon::parse($this->dateCreatedDriver))
                    ->exists()
            ) {
                $leadOrderAssignments = LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->where('created_at', '>=', Carbon::parse($this->dateCreatedDriver))
                    ->whereNull('external_id')
                    ->get();

                foreach ($leadOrderAssignments as $leadOrderAssignment) {
                    $externalId = Carbon::parse($leadOrderAssignment->created_at)->toDateString()
                        . $leadOrderAssignment->lead->email;

                    if ($externalId !== $leadOrderAssignment->external_id) {
                        $leadOrderAssignment->update([
                            'external_id' => $externalId
                        ]);
                        $counter++;
                    }
                }
            }
            $this->info("Update $counter external_id for destination {$destination->id} ({$destination->name})");
        }

        return 0;
    }
}
