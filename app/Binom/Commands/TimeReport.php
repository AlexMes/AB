<?php

namespace App\Binom\Commands;

use App\Deposit;
use App\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TimeReport extends Command
{
    protected $signature   = 'binom:report';
    protected $description = 'One off report for leads conversion ';

    public function handle()
    {
        $conversions = $this->conversions();

        $this->info('Conversions :' . $conversions->count());


        $this->table(['range','leads','deps','cr'], [
            '180-60' => $this->range($conversions, 60, 180),
            '60-30'  => $this->range($conversions, 30, 60),
            '30-5'   => $this->range($conversions, 5, 30),
            '3-3'    => $this->range($conversions, 3, 5),
            '3-0'    => $this->range($conversions, 0, 3),
            'TOTAL'  => $this->range($conversions, 0, 98000)
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection $conversions
     * @param int                            $min
     * @param int                            $max
     *
     * @return mixed
     */
    protected function range($conversions, $min, $max)
    {
        $forRange =  $conversions->filter(function ($conversion) use ($min, $max) {
            return $conversion['seconds'] >= ($min * 60) && $conversion['seconds'] <= ($max * 60);
        });

        $this->info('range ' . $min . '-' . $max);
        $leads = Lead::whereIn('clickid', $forRange->pluck('click_id'))->get(['id','clickid']);
        $deps  = Deposit::whereIn('lead_id', $leads->pluck('id')->values())->count();

        return [
            "$min-$max",
            $leads->count(),
            $deps,
            ($leads->count() > 0) ? ($deps / $leads->count()) * 100 : 0,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    protected function conversions()
    {
        return collect(app('binom')->getConversions())
            ->map(function ($conversion) {
                return Arr::only($conversion, [
                    'click_id',
                    'period',
                    'time',
                    'click_time'
                ]);
            })
            ->map(function ($conversion) {
                $conversion['seconds'] = Carbon::parse(
                    $conversion['click_time']
                )->diffInSeconds($conversion['time']);

                return $conversion;
            });
    }
}
//
//3-1 час: лиды / депы
//60-30 мин: лиды / депы
//30-5 мин: лиды / депы
//5-3 мин: лиды / депы
//3-0 мин: лиды / депы
