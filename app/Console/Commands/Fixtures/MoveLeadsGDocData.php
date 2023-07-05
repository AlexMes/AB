<?php

namespace App\Console\Commands\Fixtures;

use App\CRM\Age;
use App\CRM\Profession;
use App\CRM\Reason;
use App\Google\DocMap;
use App\Lead;
use Illuminate\Console\Command;

class MoveLeadsGDocData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:move-leads-gdoc-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moves data from gdoc columns to common.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::query()
            ->whereNotNull('gdoc_manager')
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', ''))
            ->where(fn ($query) => $query->whereNull('reject_reason')->orWhere('reject_reason', ''))
            ->where(fn ($query) => $query->whereNull('profession')->orWhere('profession', ''))
            ->where(fn ($query) => $query->whereNull('age')->orWhere('age', ''))
            ->orderBy('id');
        $progress   = $this->output->createProgressBar($leads->count());

        /** @var Lead $lead */
        foreach ($leads->cursor() as $lead) {
            $lead->called_at     = $lead->called_at ?: $lead->gdoc_called_at;
            $lead->status        = DocMap::STATUSES[$lead->gdoc_status] ?? null;
            $lead->reject_reason = DocMap::REASONS[$lead->gdoc_note] ?? null;
            if (!empty($lead->gdoc_gender)) {
                $lead->gender_id     = DocMap::GENDERS[$lead->gdoc_gender] ?? $lead->gender_id ?: 0;
            }
            $lead->profession    = DocMap::PROFESSIONS[$lead->gdoc_profession] ?? null;
            $lead->age           = DocMap::AGES[$lead->gdoc_age] ?? null;
            $lead->deposit_sum   = $lead->deposit_sum ?: $lead->gdoc_deposit_sum;
            $lead->comment       = $lead->comment ?: $lead->gdoc_comment;

            $lead->save(['timestamps' => false]);

            $progress->advance();
        }
        $progress->finish();

        return 0;
    }
}
