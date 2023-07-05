<?php

namespace App\Providers;

use App\Services\SmppService;
use App\Services\SmsClubService;
use App\Services\SmsEpochta;
use App\Services\SmsProsto;
use App\Services\SmsService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        switch (config('services.sms')) {
            case 'smpp':
                $service = SmppService::class;
                break;
            case 'prosto':
                $service = SmsProsto::class;
                break;
            case 'epochta':
                $service = SmsEpochta::class;
                break;
            default:
                $service = SmsClubService::class;
        }
        $this->app->bind(SmsService::class, $service);
    }

    public function provides()
    {
        return [SmsService::class];
    }
}
