<?php

namespace App\Diagnostic\Http\Controllers;

use App\Facebook\FacebookApp;
use App\Http\Controllers\Controller;

/**
 * Class ApplicationRoles
 *
 * @package App\Diagnostic\Http\Controllers
 */
class ApplicationRoles extends Controller
{
    /**
     * Collect application users
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function __invoke()
    {
        $roles = FacebookApp::first()->facebook()->get(sprintf("/%s/roles", config('facebook.app_id')), config('services.facebook.token'))->getDecodedBody();

        return collect($roles['data'])->map(function ($user) {
            return [
                'id'    => $user['user'],
                'role'  => $user['role'],
                'image' => sprintf("https://graph.facebook.com/%s/picture?type=normal", $user['user'])
            ];
        });
    }
}
