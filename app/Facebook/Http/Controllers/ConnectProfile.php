<?php

namespace App\Facebook\Http\Controllers;

use App\Facebook\Facebook;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConnectProfile extends Controller
{
    /**
     * Required scopes for application
     *
     * @var array
     */
    protected $scopes = [
        'email',
        'ads_management',
        'ads_read',
        'leads_retrieval',
        'read_insights',
        'pages_manage_ads',
        'pages_manage_metadata',
        'pages_read_engagement',
        'pages_read_user_content',
        'pages_manage_posts',
        'pages_manage_engagement'
    ];

    /**
     * ConnectFacebookAccount constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web']);
    }

    /**
     * Redirect user and ask for account permissions
     *
     * @param Request $request
     *
     * @return string
     */
    public function __invoke(Request $request)
    {
        return redirect(Facebook::build($request->app_id)
            ->getOAuth2Client()
            ->getAuthorizationUrl(
                Facebook::cache($request->app_id)->callback_route,
                json_encode([
                    'st'      => Str::random(40),
                    'app'     => Facebook::cache($request->app_id)->id,
                    'user_id' => auth()->id(),
                ]),
                $this->scopes,
            ));
    }
}
