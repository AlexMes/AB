<?php

namespace App\Console\Commands;

use App\Facebook\Account;
use App\Insights;
use Illuminate\Console\Command;

class ReportsBalanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ids = Insights::distinct()->select(['account_id'])
            ->whereBetween('date', ['2020-02-01','2020-02-29'])
            ->get();

        $accounts = Account::whereIn('account_id', $ids->pluck('account_id')->values())->get();

        $over400 = $accounts->filter(function ($account) {
            return (float)$account->balance >= 400;
        });

        $a = [
            'all'        => $over400->count(),
            'active'     => $over400->whereStrict('status', 'ACTIVE')->count(),
            'avgBalance' => $over400->avg(function ($rk) {
                return (float)$rk->balance;
            }),
            'totalBalance' => $over400->sum(function ($rk) {
                return (float)$rk->balance;
            })
        ];


        $under400Over100 = $accounts->filter(function ($account) {
            return (float)$account->balance < 400 && (float)$account->balance >= 100;
        });

        $b = [
            'all'        => $under400Over100->count(),
            'active'     => $under400Over100->whereStrict('status', 'ACTIVE')->count(),
            'avgBalance' => $under400Over100->avg(function ($rk) {
                return (float)$rk->balance;
            }),
            'totalBalance' => $under400Over100->sum(function ($rk) {
                return (float)$rk->balance;
            })
        ];


        $over50under100 = $accounts->filter(function ($account) {
            return (float)$account->balance < 100 && $account->balance >= 50;
        });

        $c = [
            'all'        => $over50under100->count(),
            'active'     => $over50under100->where('status', 'ACTIVE')->count(),
            'avgBalance' => $over50under100->avg(function ($rk) {
                return (float)$rk->balance;
            }),
            'totalBalance' => $over50under100->sum(function ($rk) {
                return (float)$rk->balance;
            })
        ];

        $under50 = $accounts->filter(function ($account) {
            return (float)$account->balance < 50;
        });

        $d = [
            'all'        => $under50->count(),
            'active'     => $under50->where('status', 'ACTIVE')->count(),
            'avgBalance' => $under50->avg(function ($rk) {
                return (float)$rk->balance;
            }),
            'totalBalance' => $under50->sum(function ($rk) {
                return (float)$rk->balance;
            })
        ];



        $this->table(['count','active','avgBalance','totalBalance'], [
            array_values($a),
            array_values($b),
            array_values($c),
            array_values($d),
        ]);
    }
}
