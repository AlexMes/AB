<?php

namespace App\Console\Commands\Fixtures;

use App\Deposit;
use App\Jobs\Leads\DetectAccount;
use App\Jobs\Leads\DetectCampaign;
use App\Lead;
use Illuminate\Console\Command;

class BindAdDataToLeadAndDeposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:bind-ad-to-lead-deposit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Binds account and campaign to lead and deposits.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leadsQuery = Lead::query()->where('created_at', '>', now()->subMonths(3)->toDateTimeString());
        $this->getOutput()->progressStart($leadsQuery->count());

        /** @var Lead $lead */
        foreach ($leadsQuery->cursor() as $lead) {
            DetectCampaign::dispatchNow($lead);
            DetectAccount::dispatchNow($lead);

            $lead->refresh();

            Deposit::whereLeadId($lead->id)->update(['account_id' => $lead->account_id]);

            $this->getOutput()->progressAdvance();
        }

        $this->getOutput()->progressFinish();

        return 0;
    }
}
