<?php

namespace App\Console\Commands;

use App\Deposit;
use App\Lead;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class AnotherDumbReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:another-shit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Another shitty report';

    /**
     * @var string[]
     */
    protected $regions = [
        'Krasnoyarskiy Kray','Krasnoyarskiy','Kemerovo Oblast','Novosibirsk Oblast','Tomsk Oblast','Irkutsk Oblast','Buryatiya Republic','Amur Oblast','Khabarovsk','Primorskiy (Maritime) Kray','Primorskiy','Magadan Oblast','Sakhalin Oblast','Sakha','Kamchatka'
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period = (new CarbonPeriod())
            ->days(1)
            ->since($this->ask('Since date'))
            ->until($this->ask('Until date') ?? now());

        $results = collect();

        foreach ($period as $day) {
            if ($day->isFriday() || $day->isSaturday() || $day->isSunday() || $day->isMonday()) {
                $leads = Lead::query()
                    ->whereHas('ipAddress', fn ($q) => $q->whereIn('region', $this->regions))
                    ->whereDate('created_at', $day)
                    ->pluck('id');

                $deposits = Deposit::whereIn('lead_id', $leads)->count();

                $results->add(['date' => $day->toDateString(),'dow' => $day->dayName, 'leads' => $leads->count(), 'deposits' => $deposits]);
            }
        }

        $this->table(['date','day','leads','deposits'], $results->toArray());
    }
}
