<?php

namespace App\Console\Commands\Fixtures;

use App\Jobs\Leads\DetectAccount;
use App\Jobs\Leads\DetectCampaign;
use App\Lead;
use Illuminate\Console\Command;

class DetectManualCampaignsAndAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:detect-manual-campaigns-and-accounts';

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
        $leads = Lead::query()
            ->where('created_at', '>', '2020-09-01')
            ->whereNull('account_id')
            ->whereNull('affiliate_id');

        $progress = $this->output->createProgressBar($leads->count());

        /** @var Lead $lead */
        foreach ($leads->cursor() as $lead) {
            DetectCampaign::dispatchNow($lead);
            DetectCampaign::dispatchNow($lead->fresh());

            if ($lead->hasDeposits()) {
                $lead->deposits()->update([
                    'account_id' => $lead->account_id,
                ]);
            }
            $progress->advance(1);

//            $this->info('Utm source is ' . $lead->utm_source . 'Campaign is ' . $lead->campaign_id . '. Account is ' . $lead->fresh()->account_id);
        }

        $progress->finish();

        return 0;
    }
}
