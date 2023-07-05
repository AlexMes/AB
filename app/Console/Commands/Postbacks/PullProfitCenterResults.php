<?php

namespace App\Console\Commands\Postbacks;

use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PullProfitCenterResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postback:collect-pcfx-leads-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from pcfx integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destinations = LeadDestination::whereIn('driver', [LeadDestinationDriver::AWFULCRM,LeadDestinationDriver::PROFITC])->get();

        foreach ($destinations as $destination) {
            $this->info('Collecting from destination '.$destination->name);
            if (LeadOrderAssignment::where('destination_id', $destination->id)->exists()) {
                $startDate = Carbon::parse(LeadOrderAssignment::query()
                    ->where('destination_id', $destination->id)
                    ->min('created_at')) ?? now()->subYear();

                $this->info(sprintf("Start date is %s", $startDate->toDateString()));
                /** @var \App\DestinationDrivers\HugeOffers $handler */
                $handler = $destination->initialize();

                try {
                    $raw = $handler->collectFtdSinceDate($startDate);
                    $this->line('collected response '.json_encode($raw));
                    $ftd = collect($raw['lead_deposits']);
                    $ftd->filter(fn ($item) => $item['status'])->each(function ($deposit) use ($destination) {
                        $this->info('Creating deposit '.$deposit['id']);
                        $this->processFtd($deposit, $destination);
                    });
                } catch (\Throwable $th) {
                    $this->warn('Erorr'. $th->getMessage());

                    continue;
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
            'date'             => Carbon::parse($deposit['time'])->toDateString(),
            'sum'              => '150',
            'phone'            => $assignment->lead->phone,
            'account_id'       => $assignment->lead->account_id,
            'user_id'          => $assignment->lead->user_id,
            'office_id'        => $assignment->route->order->office_id,
            'offer_id'         => $assignment->lead->offer_id,
        ]);
    }

    /**
     * Update assignments statuses
     *
     * @param \App\LeadDestination           $destination
     * @param \Illuminate\Support\Collection $collection
     *
     * @return void
     */
    public function updateAssignments(LeadDestination $destination, Collection $collection)
    {
        // suboptimal way to sync statuses, should upgrade to L8, and use `upsert` command
        $collection->filter(fn ($item) => $item['click_id'] !== null && $item['click_id'] !== '')
            ->each(fn ($result)        => $destination->assignments()->where('external_id', $result['click_id'])->update([
                'status' => $result['lead_status'],
            ]));
    }
}
