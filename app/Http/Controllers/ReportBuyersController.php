<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ReportBuyersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return User[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Request $request)
    {
        return User::visible()->orderBy('report_sort')->get(['id', 'name']);
    }
}
