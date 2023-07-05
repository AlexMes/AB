<?php

namespace App\Console\Commands;

use App\Binom\Exceptions\BinomReponseException;
use App\Jobs\PullClickInfo;
use App\Lead;
use Illuminate\Console\Command;
use Log;

class CollectClicksInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:collect-clicks-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect clicks data from Binom';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $leads = Lead::whereNotNull('clickid')->whereDoesntHave('click');

        $progress = $this->output->createProgressBar($leads->count());

        foreach ($leads->cursor() as $lead) {
            try {
                PullClickInfo::dispatchNow($lead);
            } catch (BinomReponseException $ex) {
                Log::debug('Click ' . $lead->clickid . ' missing from Binom');
            }

            $progress->advance();
        }
        $progress->finish();
    }
}
