<?php

namespace App\Http\Controllers;

use App\LeadDestinationDriver;
use Illuminate\Http\Request;

class LeadDestinationDriverController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return LeadDestinationDriver[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return LeadDestinationDriver::all();
    }
}
