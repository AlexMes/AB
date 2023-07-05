<?php

namespace App\Console\Commands;

use App\Bot\Telegram;
use App\Notifications\Reports\Statistic;
use Illuminate\Console\Command;

/**
 * Class BroadcastPreviousDayResults
 *
 * @package App\Console\Commands
 *
 * TODO: DROP THIS SHIT
 */
class BroadcastPreviousDayResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:previous-day-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications with previous day statistic to users';

    /**
     * Execute the console command.
     *
     * @param \App\Bot\Telegram $telegram
     *
     * @return mixed
     */
    public function handle(Telegram $telegram)
    {
        $telegram->say((new Statistic())->forDate(now()->subDay())->message())->to(['128578232', '46933258'])->send();
    }
}
