<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\NotificationType;
use App\User;
use Illuminate\Http\Request;

class DeniedTelegramNotificationController extends Controller
{

    /**
     * Gets list of telegram notification settings
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $user)
    {
        return response()->json($user->deniedTelegramNotifications, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(User $user, Request $request)
    {
        if ($user->id !== \Auth::id() && !\Auth::user()->isAdmin()) {
            abort(403, 'This action is unauthorized');
        }

        $notification = NotificationType::findOrFail($request->get('id'));

        $user->deniedTelegramNotifications()->attach($notification);

        return response()->json($notification, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User        $user
     * @param NotificationType $deniedTelegramNotification
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, NotificationType $deniedTelegramNotification)
    {
        if ($user->id !== \Auth::id() && !\Auth::user()->isAdmin()) {
            abort(403, 'This action is unauthorized');
        }

        $user->deniedTelegramNotifications()->detach($deniedTelegramNotification);

        return response(null, 204);
    }
}
