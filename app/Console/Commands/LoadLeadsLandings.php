<?php

namespace App\Console\Commands;

use App\Domain;
use App\Lead;
use Illuminate\Console\Command;

class LoadLeadsLandings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:attach-landings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $progress = $this->output->createProgressBar(Lead::whereNull('landing_id')->count());


        foreach (Lead::whereNull('landing_id')->cursor() as $lead) {
            $lead->update([
                'landing_id' => optional(Domain::landing()->where('url', 'like', '%' . $lead->domain . '%')->first())->id
            ]);
            $progress->advance();
        }

        $progress->finish();
    }
}
