<?php


namespace App\Bot;

use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->when(Telegram::class)
            ->needs('$token')
            ->give(config('telegram.token'));

        $this->app->when(TelegramChannel::class)
            ->needs(Telegram::class)
            ->give(static function () {
                return new Telegram(config('telegram.token'));
            });
    }
}
