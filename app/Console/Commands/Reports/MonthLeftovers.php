<?php

namespace App\Console\Commands\Reports;

use App\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MonthLeftovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:show-leftovers-for-month';

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
            ->select([
                DB::raw('date(leads.created_at) as registration'),
                DB::raw('offers.name as offer'),
                DB::raw('count(leads.id) as count'),
            ])
            ->leftovers([
                'since' => now()->startOfMonth()->toDateString(),
                'until' => now()->endOfMonth()->toDateString(),
            ])
            ->join('offers', 'leads.offer_id', '=', 'offers.id')
            ->groupByRaw('registration, offer')
            ->get();

        $this->table(['date','offer','leads'], $leads);

        return 0;
    }
}
