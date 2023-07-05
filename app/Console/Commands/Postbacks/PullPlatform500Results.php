<?php

namespace App\Console\Commands\Postbacks;

use App\DestinationDrivers\Platform500;
use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PullPlatform500Results extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-platform500-leads-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from Platform500 integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::PLATFORM500)->get();

        foreach ($destinations as $destination) {
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $firstAssignmentAt = LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->min('created_at');

                $startDate = Carbon::parse($firstAssignmentAt);

                /** @var Platform500 $handler */
                $handler = $destination->initialize();

                try {
                    $ftd = $handler->collectFtdSinceDate($startDate);
                } catch (\Throwable $th) {
                    continue;
                }

                collect($ftd)->each(fn ($deposit) => $this->handleFtd($deposit, $destination));
            }
        }

        return 0;
    }

    public function handleFtd($deposit, $destination)
    {
        $assignment = LeadOrderAssignment::query()
            ->with('lead')
            ->whereExternalId($deposit['lead_id'])
            ->where('destination_id', $destination->id)
            ->first();

        if ($assignment == null || $assignment->hasDeposit()) {
            return;
        }

        $assignment->lead->deposits()->create([
            'lead_return_date' => $assignment->created_at,
            'date'             => Carbon::parse($deposit['created_at'])->toDateString(),
            'sum'              => '151',
            'phone'            => $assignment->lead->phone,
            'account_id'       => $assignment->lead->account_id,
            'user_id'          => $assignment->lead->user_id,
            'office_id'        => $assignment->route->order->office_id,
            'offer_id'         => $assignment->lead->offer_id,
        ]);
    }
}
