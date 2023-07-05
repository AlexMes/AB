<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Notifications\DatabaseNotification;

class MarkNotificationRead extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param User                 $user
     * @param DatabaseNotification $notification
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $user, DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response($notification, 202);
    }
}
