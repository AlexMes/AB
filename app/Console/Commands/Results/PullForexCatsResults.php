<?php

namespace App\Console\Commands\Results;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class PullForexCatsResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-forex-cats-leads-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from forexcas integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::FOREXCATS)->get();

        foreach ($destinations as $destination) {
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $startDate = Carbon::parse(LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->min('created_at'));

                $this->info(sprintf("Start date is %s. Destination is %s", $startDate->toDateString(), $destination->name));
                /** @var \App\DestinationDrivers\HugeOffers $handler */
                $handler = $destination->initialize();


                try {
                    $raw = $handler->collectFtdSinceDate($startDate);
                    if ($raw === null) {
                        throw new Exception("empty response from destination", 1);
                    }
                } catch (\Throwable $th) {
                    continue;
                }
                $this->info(json_encode($raw));


                $ftd = collect($raw['data']);


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
            ->whereExternalId($deposit['lead_id'])
            ->where('destination_id', $destination->id)
            ->first();

        if ($assignment === null || $assignment->hasDeposit()) {
            return;
        }

        $assignment->lead->deposits()->create([
            'lead_return_date' => $assignment->created_at,
            'date'             => Carbon::parse($deposit['deposit_date'])->toDateString(),
            'sum'              => 150,
            'phone'            => $assignment->lead->phone,
            'account_id'       => $assignment->lead->account_id,
            'user_id'          => $assignment->lead->user_id,
            'office_id'        => $assignment->route->order->office_id,
            'offer_id'         => $assignment->lead->offer_id,
        ]);
    }
}
