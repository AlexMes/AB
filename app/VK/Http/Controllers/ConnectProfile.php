<?php

namespace App\VK\Http\Controllers;

use App\Http\Controllers\Controller;
use App\VK\Models\Profile;
use App\VK\VKApp;
use Illuminate\Http\Request;

class ConnectProfile extends Controller
{
    /**
     * Required scopes for application
     *
     * @var array
     */
    protected $scopes = [
        'email',
        'ads',
        'pages',
        'groups',
        /*'stats',*/
        'offline', // expire_in=0
    ];

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param VKApp                    $vkApp
     *
     *@throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     *
     */
    public function __invoke(Request $request, VKApp $vkApp)
    {
        $this->authorize('create', Profile::class);

        return redirect($vkApp->getLoginHelper()->getLoginUrl($this->scopes));
    }
}
