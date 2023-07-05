<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Console\Command;

class FixLeadStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:set-statuses-from-gdoc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update leads statuses';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function handle()
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

        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['Отказ','отказ'])->update(['status' => 'Отказ']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['депозит','Депозит'])->update(['status' => 'Депозит']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['Перезвон','перезвон','перезон','перезвон у пк'])->update(['status' => 'Перезвон']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['Демонтрация','демонстрация','деонстрация','Демонстрация'])->update(['status' => 'Демонстрация']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['В работе у клоузера','в работе у клоузера'])->update(['status' => 'В работе у клоузера']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['Дубль','дубль'])->update(['status' => 'Дубль']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['нбт','НБТ'])->update(['status' => 'Нет ответа']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereIn('gdoc_status', ['неверный номер'])->update(['status' => 'Неверный номер']);

        Lead::whereIn('id', $assignments)->whereNull('status')->whereGdocStatus(null)->whereHas('assignments')->update(['status' => 'Новый']);
        Lead::whereIn('id', $assignments)->whereNull('status')->whereGdocStatus('Новый')->whereHas('assignments')->update(['status' => 'Новый']);
    }
}
