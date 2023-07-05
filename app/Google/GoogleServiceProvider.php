<?php

namespace App\Google;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Sheets;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    use Concenrs\EventMap;

    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('google', function () {
            $client = new Google_Client();
            $client->setApplicationName('AdsBoard App');
            $client->setScopes([Google_Service_Sheets::SPREADSHEETS, Google_Service_Drive::DRIVE]);
            $client->setAuthConfig(json_decode(Storage::get('credentials.json'), true));
            $client->setAccessType('offline');

            return $client;
        });

        $this->app->singleton('sheets', function ($app) {
            return new Google_Service_Sheets($app['google']);
        });

        $this->app->singleton('drive', function ($app) {
            return new Google_Service_Drive($app['google']);
        });
    }

    public function boot()
    {
        $this->registerEvents();
    }
}
