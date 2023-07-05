<?php

namespace App\Http\Controllers;

use App\Hosting;

class HostingsController extends Controller
{
    /**
     * Get all hostings
     *
     * @return Hosting[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Hosting::all();
    }
}
