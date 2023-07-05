<?php

namespace App\Console\Commands;

use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\Offer;
use Illuminate\Console\Command;

class DispatchOldLeadsDistribution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:distribute-leftovers';

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
        foreach (
            Lead::leftovers(['2020-04-07', now()->toDateString()])
                ->whereIn('offer_id', $this->getOffersWithOrdersForToday())
                ->latest()
                ->cursor() as $lead
        ) {
            LeadAssigner::dispatch($lead);
        }

        return 0;
    }

    /**
     * Get offers with old leads, which was ordered for today
     *
     * @return array
     */
    protected function getOffersWithOrdersForToday(): array
    {
        return Offer::leftovers()
            ->whereHas('routes', fn ($query) => $query->active()->current())
            ->pluck('id')
            ->toArray();
    }
}
