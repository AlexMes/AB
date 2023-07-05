<?php

namespace App\Http\Controllers\Enums;

use App\Http\Controllers\Controller;
use App\Offer;
use Illuminate\Http\Request;

class Offers extends Controller
{
    /**
     * Get all offers
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Offer[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Request $request)
    {
        return Offer::all();
    }
}
