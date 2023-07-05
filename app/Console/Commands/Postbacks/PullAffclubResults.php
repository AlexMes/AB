<?php

namespace App\Console\Commands\Postbacks;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PullAffclubResults extends Command
{
    public const INTERVAL_DAYS = 30;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-affclub-leads-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from Affclub integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereDriver(LeadDestinationDriver::AFFCLUB)->get();

        foreach ($destinations as $destination) {
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $startDate = Carbon::parse(LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->min('created_at'));

                // API allows 30 days interval
                $period = $startDate->toPeriod(now(), self::INTERVAL_DAYS + 1, 'day');
                foreach ($period as $startPeriod) {
                    $endPeriod = $startPeriod->copy()->addDays(self::INTERVAL_DAYS)->min(now());

                    $this->info(sprintf("Start period is %s", $startPeriod->toDateString()));
                    $this->info(sprintf("End period is %s", $endPeriod->toDateString()));
                    /** @var \App\DestinationDrivers\Affclub $handler */
                    $handler = $destination->initialize();

                    try {
                        $raw = $handler->collectFtdSinceDate($startPeriod, $endPeriod);
                    } catch (\Throwable $th) {
                        continue;
                    }

                    $this->info(json_encode($raw));
                    $ftd = collect($raw['result']);

                    $ftd->each(function ($deposit) use ($destination) {
                        $this->info(sprintf("Working on deposit %s", json_encode($deposit)));
                        $this->processFtd($deposit, $destination);
                    });
                }
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
        if (! array_key_exists('ftd_date', $deposit) || $deposit['ftd_date'] === null) {
            return;
        }

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
            'date'             => Carbon::parse($deposit['ftd_date'])->toDateString(),
            'sum'              => 150,
            'phone'            => $assignment->lead->phone,
            'account_id'       => $assignment->lead->account_id,
            'user_id'          => $assignment->lead->user_id,
            'office_id'        => $assignment->route->order->office_id,
            'offer_id'         => $assignment->lead->offer_id,
        ]);
    }
}
