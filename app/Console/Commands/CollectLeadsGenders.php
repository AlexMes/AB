<?php

namespace App\Console\Commands;

use App\Jobs\Leads\DetectGender;
use App\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CollectLeadsGenders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:collect-genders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load lead genders';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $q        = Lead::where('gender_id', 0);
        $progress = $this->output->createProgressBar($q->count());

        foreach ($q->orderBy('created_at')->cursor() as $lead) {
            try {
                DetectGender::dispatch($lead);
            } catch (\Throwable $exception) {
                Log::debug('cant detect gender on lead ' . $lead->id);
            }
            $progress->advance();
        }

        $progress->finish();
    }
}
