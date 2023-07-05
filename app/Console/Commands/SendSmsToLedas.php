<?php

namespace App\Console\Commands;

use App\SmsCampaign;
use Illuminate\Console\Command;

class SendSmsToLedas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send sms to leads';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        SmsCampaign::query()
            ->active()
            ->delayed()
            ->each(function (SmsCampaign $campaign) {
                $campaign->run();
            });

        $this->info('Sms campaign run dispatched');
    }
}
