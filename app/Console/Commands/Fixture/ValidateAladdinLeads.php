<?php

namespace App\Console\Commands\Fixture;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Services\MessageBird\MessageBird;
use Illuminate\Console\Command;

class ValidateAladdinLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:validate-lead-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate phone numbers format for Aladdin office in Jul';

    /**
     * Execute the console command.
     *
     * @param \App\Services\MessageBird\MessageBird $messageBird
     *
     * @return int
     */
    public function handle(MessageBird $messageBird)
    {
        $assignments = LeadOrderAssignment::query()
            ->whereIn(
                'route_id',
                LeadOrderRoute::whereIn(
                    'order_id',
                    LeadsOrder::whereNotIn('office_id', [29])
                        ->whereBetween('date', ['2020-07-01','2020-07-31'])
                        ->pluck('id')
                )->pluck('id')
            )
            ->pluck('lead_id');


        $leads = Lead::whereIn('id', $assignments);

        foreach ($leads as $lead) {
            /** @var \App\Lead $lead */
            $lead->phone_valid = $messageBird->isValid($lead->formatted_phone);

            $lead->saveQuietly();
        }

        return 0;
    }
}
