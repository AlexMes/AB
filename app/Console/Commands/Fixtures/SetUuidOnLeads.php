<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetUuidOnLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:set-uuid-on-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set UUID on leads, where it is not set';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::whereNull('uuid');

        $progress = $this->output->createProgressBar($leads->count());

        foreach ($leads->cursor() as $lead) {
            $lead->uuid = Str::uuid()->toString();

            $lead->save(['timestamps' => false]);

            $progress->advance();
        }

        $progress->finish();

        return 0;
    }
}
