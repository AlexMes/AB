<?php

namespace App\Http\Controllers;

use App\CRM\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Status[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Status::query()->get(['name']);
    }
}
