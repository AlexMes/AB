<?php

namespace App\Console\Commands\Fixture;

use App\DestinationDrivers\Contracts\Bitrix24;
use App\Jobs\Leads\SetBitrixStatus;
use App\LeadDestination;
use App\LeadOrderAssignment;
use App\LeadsOrder;
use Illuminate\Console\Command;

class CollectPostbackFromFedotov extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:collect-postbacks-from-fedotov';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect statuses and deposits from Fedotov Bitrix24';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assignments = LeadOrderAssignment::whereHas('route', function ($query) {
            return $query->whereIn('order_id', LeadsOrder::whereOfficeId(23)->pluck('id'));
        });

        /** @var LeadOrderAssignment $assignment */
        foreach ($assignments->cursor() as $assignment) {
            $this->info($this->process($assignment));
            sleep(1);
        }

        return 0;
    }

    /**
     * Load info from Bitrix24, and corresponding lead info
     *
     * @param LeadOrderAssignment $leadOrderAssignment
     *
     * @return string
     */
    protected function process(LeadOrderAssignment $leadOrderAssignment)
    {
        if ($leadOrderAssignment->external_id === null) {
            return 'missing external id';
        }

        $this->info(sprintf(
            "%s(ass_id:%s) %s",
            $leadOrderAssignment->lead_id,
            $leadOrderAssignment->id,
            $leadOrderAssignment->status
        ));

        $dst = LeadDestination::find(1)->initialize();

        SetBitrixStatus::dispatchNow($leadOrderAssignment, $dst);

        return $leadOrderAssignment->refresh()->status;
    }
}
