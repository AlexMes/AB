<?php

namespace App\Gamble\Http\Controllers;

use App\Gamble\Country;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json(Country::all());
    }
}
