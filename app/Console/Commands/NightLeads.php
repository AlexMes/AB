<?php

namespace App\Console\Commands;

use App\Lead;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class NightLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:night';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $s     = $day->format('Y-m-d 21:00:00');
            $e     = $day->addDay()->format('Y-m-d 06:00:00');
            $leads = Lead::query()
                ->whereHas('ipAddress', fn ($q) => $q->whereIn('region', $this->regions))
                ->whereBetween('created_at', [$s,$e])
                ->pluck('id');

            $results->add(['from' => $s,'until' => $e,'leads' => $leads]);
        }

        $this->table(['since','until','leads'], $results->toArray());
    }
}
