<?php

namespace App\Console\Commands;

use App\Lead;
use App\LeadOrderAssignment;
use App\Offer;
use Illuminate\Console\Command;

class CollectLeadsForDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:leads-duplicate
                            {--branch=19 : branch to select leads from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collecting leads of selected branch and create duplicates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $between = [now()->yesterday()->startOfDay(),now()->yesterday()->endOfDay()];

        $duplicateLeads = Lead::select(['id', 'phone'])
            ->whereIn('offer_id', Offer::duplicates()->pluck('id'))
            ->when(
                $this->option('branch'),
                fn ($query) => $query->whereHas('user', fn ($q) => $q->where('branch_id', $this->option('branch')))
            )
            ->get();

        $leads = LeadOrderAssignment::query()
            ->select(['lead_id'])
            ->with(['lead'])
            ->when(
                $this->option('branch'),
                fn ($query) => $query->whereHas('lead.user', fn ($q) => $q->where('branch_id', $this->option('branch')))
            )
            ->whereBetween('created_at', $between)
            ->whereIn('status', [
                'Na zameny', 'Dubl', 'Дубликат', 'Дубль', 'Return'
            ])
            // skip duplicates and its originals
            ->whereDoesntHave('lead', fn ($query) => $query->whereIn('phone', $duplicateLeads->pluck('phone')))
            ->whereNotIn('lead_id', $duplicateLeads->pluck('id'))
            ->get()
            ->pluck('lead')
            ->unique('id');

        /** @var Lead $lead */
        foreach ($leads as $lead) {
            dispatch(fn () => $lead->createDuplicate());
        }

        return 0;
    }
}
