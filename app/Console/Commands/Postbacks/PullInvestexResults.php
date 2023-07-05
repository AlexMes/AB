<?php

namespace App\Console\Commands\Postbacks;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PullInvestexResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-investex-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from Edeal integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::INVESTEX)->get();

        foreach ($destinations as $destination) {
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $startDate = Carbon::parse(LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->min('created_at'));

                $this->info(sprintf("Start date is %s", $startDate->toDateString()));
                /** @var \App\DestinationDrivers\Investex $handler */
                $handler = $destination->initialize();

                try {
                    $raw = $handler->collectFtdSinceDate($startDate);
                } catch (\Throwable $th) {
                    continue;
                }

                $ftd = collect($raw['lead_deposits'])->filter(fn ($lead) => $lead['status']);

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
            'sum'              => $deposit['commission'] ?? 150,
            'phone'            => $assignment->lead->phone,
            'account_id'       => $assignment->lead->account_id,
            'user_id'          => $assignment->lead->user_id,
            'office_id'        => $assignment->route->order->office_id,
            'offer_id'         => $assignment->lead->offer_id,
        ]);
    }
}
