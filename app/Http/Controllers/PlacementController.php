<?php

namespace App\Http\Controllers;

use App\Placement;

class PlacementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Placement[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Placement::all();
    }
}
