<?php

namespace App\Console\Commands;

use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Console\Command;

class SayHiFromBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @throws \BotMan\BotMan\Exceptions\Base\BotManException
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var \BotMan\BotMan\BotMan $bot */
        $bot = app('bot');
        $bot->say('Hola yoba', '46933258', TelegramDriver::class);
    }
}
