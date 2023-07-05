<?php

namespace App\Console\Commands\Report;

use App\Deposit;
use App\Facebook\Account;
use App\Insights;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StatsBeforeAndAfter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rows = collect($this->data())->map(function ($row) {
            $date = Carbon::parse($row['date']);
            $account = Account::where('account_id', $row['account'])->first();

            return  [
                'date'           => $date,
                'accountId'      => $row['account'],
                'accountName'    => optional($account)->name,
                'spendBefore'    => Insights::query()->where('account_id', $row['account'])->where('date', '<', $date->toDateString())->sum(\DB::raw('spend::decimal')),
                'leadsBefore'    => Insights::query()->where('account_id', $row['account'])->where('date', '<', $date->toDateString())->sum('leads_cnt'),
                'depositsBefore' => Deposit::where('account_id', $row['account'])->whereDate('lead_return_date', '<', $date->toDateString())->count(),
                'spendAfter'     => Insights::query()->where('account_id', $row['account'])->where('date', '>=', $date->toDateString())->sum(\DB::raw('spend::decimal')),
                'leadsAfter'     => Insights::query()->where('account_id', $row['account'])->where('date', '>=', $date->toDateString())->sum('leads_cnt'),
                'depositsAfter'  => Deposit::where('account_id', $row['account'])->whereDate('lead_return_date', '>=', $date->toDateString())->count(),
                'status'         => optional($account)->status,
                'updatedAt'      => optional($account)->updated_at,
            ];
        });

        $this->table(array_keys($rows->first()), $rows);
    }


    protected function data(): array
    {
        return [
            ['date' => '06.02.2020',  'account' => '463313730985214'],
            ['date' => '06.02.2020', 'account' => '774238199741087'],
            ['date' => '06.02.2020', 'account' => '997248197316792'],
            ['date' => '06.02.2020', 'account' => '3049222911775150'],
            ['date' => '06.02.2020', 'account' => '605084693569593'],
            ['date' => '06.02.2020', 'account' => '2595671177323130'],
            ['date' => '06.02.2020', 'account' => '162244075058240'],
            ['date' => '06.02.2020', 'account' => '157089459053076'],
            ['date' => '06.02.2020', 'account' => '579925102825590'],
            ['date' => '06.02.2020', 'account' => '485398412401946'],
            ['date' => '06.02.2020', 'account' => '914321232095715'],
            ['date' => '06.02.2020', 'account' => '752855798404814'],
            ['date' => '06.02.2020', 'account' => '454549825453920'],
            ['date' => '06.02.2020', 'account' => '468598857165534'],
            ['date' => '06.02.2020', 'account' => '2545517142330500'],
            ['date' => '06.02.2020', 'account' => '190628345466222'],
            ['date' => '06.02.2020', 'account' => '3054404201451450'],
            ['date' => '06.02.2020', 'account' => '2625528947532970'],
            ['date' => '06.02.2020', 'account' => '2625528947532970'],
            ['date' => '06.02.2020', 'account' => '1247912872063970'],
            ['date' => '06.02.2020', 'account' => '2768824336489300'],
            ['date' => '06.02.2020', 'account' => '1014824245550940'],
            ['date' => '06.02.2020', 'account' => '1014824245550940'],
            ['date' => '06.02.2020', 'account' => '130652828044796'],
            ['date' => '06.02.2020', 'account' => '130652828044796'],
            ['date' => '06.02.2020', 'account' => '453707635201666'],
            ['date' => '06.02.2020', 'account' => '736911630125683'],
            ['date' => '06.02.2020', 'account' => '1772382579565780'],
            ['date' => '06.02.2020', 'account' => '2595946637306770'],
            ['date' => '08.02.2020', 'account' => '216443906048782'],
            ['date' => '08.02.2020', 'account' => '2402801939936520'],
            ['date' => '08.02.2020', 'account' => '239785963674260'],
            ['date' => '08.02.2020', 'account' => '494073591461612'],
            ['date' => '08.02.2020', 'account' => '755812304890279'],
            ['date' => '08.02.2020', 'account' => '1352084871630940'],
            ['date' => '08.02.2020', 'account' => '190628345466222'],
            ['date' => '08.02.2020', 'account' => '453707635201666'],
            ['date' => '08.02.2020', 'account' => '2595946637306770'],
            ['date' => '08.02.2020', 'account' => '736911630125683'],
            ['date' => '08.02.2020', 'account' => '2545517142330500'],
            ['date' => '08.02.2020', 'account' => '697752500754742'],
            ['date' => '08.02.2020', 'account' => '534707530707128'],
            ['date' => '08.02.2020', 'account' => '1014824245550940'],
            ['date' => '09.02.2020', 'account' => '453408915224668'],
            ['date' => '09.02.2020', 'account' => '790880244420288'],
            ['date' => '09.02.2020', 'account' => '632725110630836'],
            ['date' => '09.02.2020', 'account' => '192573415187937'],
            ['date' => '09.02.2020', 'account' => '273227013291069'],
            ['date' => '10.02.2020', 'account' => '586627418750081'],
            ['date' => '10.02.2020', 'account' => '331315387759064'],
            ['date' => '10.02.2020', 'account' => '641856393290548'],
            ['date' => '10.02.2020', 'account' => '1247912872063978'],
            ['date' => '10.02.2020', 'account' => '226933681637059'],
            ['date' => '10.02.2020', 'account' => '1021438894907003'],
            ['date' => '10.02.2020', 'account' => '2512845648958660'],
            ['date' => '11.02.2020', 'account' => '113969946632945'],
            ['date' => '11.02.2020', 'account' => '431271324185462'],
            ['date' => '11.02.2020', 'account' => '582232365890846'],
            ['date' => '11.02.2020', 'account' => '3229557130406473'],
            ['date' => '11.02.2020', 'account' => '2402801939936520'],
            ['date' => '11.02.2020', 'account' => '1207028522819628'],
            ['date' => '12.02.2020', 'account' => '1216129275202174'],
            ['date' => '12.02.2020', 'account' => '2484115728467799'],
            ['date' => '12.02.2020', 'account' => '858622130992757'],
            ['date' => '12.02.2020', 'account' => '623962131742421'],
            ['date' => '12.02.2020', 'account' => '3054404201451455'],
            ['date' => '12.02.2020', 'account' => '2625528947532973'],
            ['date' => '13.02.2020', 'account' => '2311582402421013'],
            ['date' => '13.02.2020', 'account' => '195029481703469'],
            ['date' => '13.02.2020', 'account' => '808498602952381'],
            ['date' => '13.02.2020', 'account' => '586627418750081'],
            ['date' => '13.02.2020', 'account' => '2558258604463138'],
            ['date' => '13.02.2020', 'account' => '2509835972670222'],
            ['date' => '13.02.2020', 'account' => '174567856360553'],
            ['date' => '14.02.2020', 'account' => '867117817082219'],
            ['date' => '14.02.2020', 'account' => '183127736378702'],
            ['date' => '14.02.2020', 'account' => '206877867028197'],
            ['date' => '14.02.2020', 'account' => '1706226662813747'],
            ['date' => '14.02.2020', 'account' => '2500197146862672'],
            ['date' => '14.02.2020', 'account' => '627427241348881'],
            ['date' => '14.02.2020', 'account' => '877504432678944'],
            ['date' => '17.02.2020', 'account' => '2593431047442054'],
            ['date' => '17.02.2020', 'account' => '1040117163025282'],
            ['date' => '17.02.2020', 'account' => '2593431047442054'],
            ['date' => '17.02.2020', 'account' => '602939280554661'],
            ['date' => '17.02.2020', 'account' => '623962131742421'],
            ['date' => '17.02.2020', 'account' => '3054404201451455'],
            ['date' => '17.02.2020', 'account' => '2625528947532973'],
            ['date' => '17.02.2020', 'account' => '480069885983452'],
            ['date' => '17.02.2020', 'account' => '631722827587690'],
            ['date' => '17.02.2020', 'account' => '495597828033951'],
            ['date' => '17.02.2020', 'account' => '226933681637059'],
            ['date' => '17.02.2020', 'account' => '2558116237639394'],
            ['date' => '17.02.2020', 'account' => '172906224119012'],
            ['date' => '17.02.2020', 'account' => '122814329109590'],
            ['date' => '17.02.2020', 'account' => '200930411058719'],
            ['date' => '17.02.2020', 'account' => '175162370575715'],
            ['date' => '17.02.2020', 'account' => '2703714406536875'],
            ['date' => '17.02.2020', 'account' => '2362673760719779'],
            ['date' => '17.02.2020', 'account' => '1218182441703707'],
            ['date' => '17.02.2020', 'account' => '2508242482632326'],
            ['date' => '17.02.2020', 'account' => '3324756420884095'],
            ['date' => '17.02.2020', 'account' => '125206438856750'],
            ['date' => '17.02.2020', 'account' => '626354198140115'],
        ];
    }
}
