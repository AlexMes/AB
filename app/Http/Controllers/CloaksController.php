<?php

namespace App\Http\Controllers;

use App\Cloak;

class CloaksController extends Controller
{
    /**
     * @return \App\Cloak[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Cloak::all();
    }
}
