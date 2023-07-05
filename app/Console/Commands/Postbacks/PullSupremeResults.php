<?php

namespace App\Console\Commands\Postbacks;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PullSupremeResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-supreme-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from supreme integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::SUPERMEDIA)->get();

        foreach ($destinations as $destination) {
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $startDate = now()->subDays(29);

                $this->info(sprintf("Start date is %s", $startDate->toDateString()));
                /** @var \App\DestinationDrivers\Investex $handler */
                $handler = $destination->initialize();

                $raw = $handler->collectFtdSinceDate($startDate);

                $ftd = collect($raw['result']);

                $ftd->each(function ($deposit) use ($destination) {
                    $this->info(sprintf("Working on deposit %s", json_encode($destination)));
                    $this->processFtd($deposit, $destination);
                });
            }
        }

        return 0;
    }

    /**
     * @param $deposit
     * @param \App\LeadDestination $destination
     */
    protected function processFtd($deposit, LeadDestination $destination): void
    {
        $assignment = LeadOrderAssignment::query()
            ->with('lead')
            ->whereExternalId($deposit['id'])
            ->where('destination_id', $destination->id)
            ->first();

        if ($assignment === null || $assignment->hasDeposit()) {
            return;
        }

        $assignment->lead->deposits()->create([
            'lead_return_date' => $assignment->created_at,
            'date'             => Carbon::parse($deposit['ftd_date'] ?? now())->toDateString(),
            'sum'              => $deposit['commission'] ?? 500,
            'phone'            => $assignment->lead->phone,
            'account_id'       => $assignment->lead->account_id,
            'user_id'          => $assignment->lead->user_id,
            'office_id'        => $assignment->route->order->office_id,
            'offer_id'         => $assignment->lead->offer_id,
        ]);
    }
}
