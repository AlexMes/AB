<?php

namespace App\Gamble\Http\Controllers;

use App\Gamble\GoogleApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SendEventToFacebook extends Controller
{
    /**
     * Handle fcking event
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function __invoke(Request $request)
    {
        $app = GoogleApp::where('market_id', $request->input('app_id'))->firstOrFail();

        $response = Http::asForm()->post(sprintf("https://graph.facebook.com/%s/activities", $app->fb_app_id), [
            'event'                        => 'CUSTOM_APP_EVENTS',
            'advertiser_id'                => $request->input('advertiser_id'),
            'advertiser_tracking_enabled'  => 1,
            'application_tracking_enabled' => 1,
            'custom_events'                => [
                json_encode([
                    '_eventName'  => $request->input('event') ?? 'fb_mobile_purchase',
                    '_valueToSum' => 20.00,
                    'fb_currency' => "USD"
                ])
            ],
            'app_access_token' => $app->fb_token
        ]);

        return $response->body();
    }
}
