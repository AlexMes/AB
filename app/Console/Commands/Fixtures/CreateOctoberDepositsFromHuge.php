<?php

namespace App\Console\Commands\Fixtures;

use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreateOctoberDepositsFromHuge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:create-october-deposits-from-huge';

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
        $assignments = LeadOrderAssignment::query()
            ->with('lead')
            ->whereIn('lead_id', [280135,280290,280074,280188])
            ->get();

        foreach ($assignments as $assignment) {
            if ($assignment->hasDeposit()) {
                continue;
            }

            $assignment->lead->deposits()->create([
                'lead_return_date' => $assignment->created_at,
                'date'             => Carbon::parse($assignment->created_at)->toDateString(),
                'sum'              => 300,
                'phone'            => $assignment->lead->phone,
                'account_id'       => $assignment->lead->account_id,
                'user_id'          => $assignment->lead->user_id,
                'office_id'        => $assignment->route->order->office_id,
                'offer_id'         => $assignment->lead->offer_id,
            ]);
        }

        return 0;
    }
}
