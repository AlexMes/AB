<?php

namespace App\Facebook\Http\Controllers;

use App\Facebook\Facebook;
use App\Facebook\Jobs\ConnectFacebookProfile;
use App\Http\Controllers\Controller;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessCallback extends Controller
{
    /**
     * Handle Facebook redirect
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function __invoke(Request $request)
    {
        try {
            $app   = Facebook::fromRequest($request);
            $state = json_decode($request->state, true);
            /** Bypass CSRF token check */
            ($helper = $app->facebook()->getRedirectLoginHelper())
                ->getPersistentDataHandler()
                ->set('state', $request->get('state'));

            ConnectFacebookProfile::dispatch($helper->getAccessToken(), $app, $state['user_id']);

            return "Your Facebook profile attached. You can close this page now";
        } catch (FacebookSDKException $e) {
            Log::error('Received failure from facebook.' . PHP_EOL . $e->getMessage());

            return response("Error occurred during profile attachment. This incident reported to developers. Error: " . $e->getMessage());
        }
    }
}
