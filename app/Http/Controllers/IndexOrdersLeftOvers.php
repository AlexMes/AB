<?php

namespace App\Http\Controllers;

use App\Lead;
use Illuminate\Http\Request;

class IndexOrdersLeftOvers extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Lead[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Lead::visible()
            ->leftovers($request->input('date'), $request->input('offers'))
            ->get(['id', 'firstname', 'lastname', 'email', 'phone']);
    }
}
