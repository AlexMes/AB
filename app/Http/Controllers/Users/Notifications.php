<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class Notifications extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(User $user, Request $request)
    {
        return $user->unreadNotifications()
            ->notEmptyWhere('type', $request->input('notification_type'))
            ->paginate();
    }
}
