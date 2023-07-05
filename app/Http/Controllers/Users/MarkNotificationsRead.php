<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class MarkNotificationsRead extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $user, Request $request)
    {
        $user->unreadNotifications->markAsRead();

        return response(null, 204);
    }
}
