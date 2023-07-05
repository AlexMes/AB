<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CleanupDuplicatedLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:cleanup-duplicated-leads';

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
            ->whereIn('domain', ['tnkfquiz.info', 'oprostnk.info'])
            ->whereDate('created_at', '2021-02-25')
            ->whereIn('phone', Lead::select('phone')
            ->whereDate('created_at', '2021-02-25')
            ->having(\DB::raw('count(phone)'), '>', 1)->groupBy('phone')->pluck('phone'))
            ->get();

        $leads->groupBy('phone')->each(function (Collection $group) {
            $shouldLeave = $group->max('id');

            LeadOrderAssignment::whereLeadId($shouldLeave)->get()->each->remove();
            Lead::find($shouldLeave)->delete();
        });

        return 0;
    }
}
