<?php

namespace App\Http\Controllers;

use App\CRM\Age;
use Illuminate\Http\Request;

class AgeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Age[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Age::query()->get(['name']);
    }
}
