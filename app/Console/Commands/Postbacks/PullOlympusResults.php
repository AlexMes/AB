<?php

namespace App\Console\Commands\Postbacks;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PullOlympusResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-olympus-leads-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from Olympus integration';

    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::OLYMPUS)->get();

        foreach ($destinations as $destination) {
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $firstAssignmentAt = LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->min('created_at');

                $startDate = Carbon::parse($firstAssignmentAt);

                /** @var \App\DestinationDrivers\Olympus $handler */
                $handler = $destination->initialize();

                try {
                    $ftd = $handler->collectFtdSinceDate($startDate);
                } catch (\Throwable $th) {
                    continue;
                }

                collect($ftd)->each(function ($deposit) use ($destination) {
                    $assignment = LeadOrderAssignment::query()
                        ->with('lead')
                        ->whereExternalId($deposit['_id'])
                        ->where('destination_id', $destination->id)
                        ->first();

                    if ($assignment->hasDeposit()) {
                        return;
                    }

                    $assignment->lead->deposits()->create([
                        'lead_return_date' => $assignment->created_at,
                        'date'             => now()->toDateString(),
                        'sum'              => '151',
                        'phone'            => $assignment->lead->phone,
                        'account_id'       => $assignment->lead->account_id,
                        'user_id'          => $assignment->lead->user_id,
                        'office_id'        => $assignment->route->order->office_id,
                        'offer_id'         => $assignment->lead->offer_id,
                    ]);
                });
            }
        }

        return 0;
    }
}
