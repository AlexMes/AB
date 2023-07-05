<?php

namespace App\Console\Commands\Postbacks;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PullGlobalWiseResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-globalwise-leads-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from Global Wise integration';

    /**
     * Execute the console command.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return int
     *
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::GLOBALWISE)->get();

        foreach ($destinations as $destination) {
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $assignmentsPeriod = LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->select([
                        DB::raw('min(created_at) as since'),
                        DB::raw('max(created_at) as until'),
                    ])->first();

                /** @var \App\DestinationDrivers\GlobalWise $handler */
                $handler = $destination->initialize();

                try {
                    $ftd = $handler->collectFtdForPeriod(
                        Carbon::parse($assignmentsPeriod->since),
                        Carbon::parse($assignmentsPeriod->until),
                    );
                } catch (\Throwable $th) {
                    continue;
                }


                collect($ftd)->each(function ($convertedLead) use ($destination) {
                    $assignment = LeadOrderAssignment::query()
                        ->with('lead')
                        ->whereExternalId($convertedLead['Id'])
                        ->where('destination_id', $destination->id)
                        ->first();

                    if ($assignment === null || $assignment->hasDeposit()) {
                        return;
                    }

                    $assignment->lead->deposits()->create([
                        'lead_return_date' => $assignment->created_at,
                        'date'             => now()->toDateString(),
                        'sum'              => $convertedLead['FtdAmount'] ?? 0.00,
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
