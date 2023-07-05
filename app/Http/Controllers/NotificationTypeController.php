<?php

namespace App\Http\Controllers;

use App\NotificationType;

class NotificationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return NotificationType[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return NotificationType::all();
    }
}
