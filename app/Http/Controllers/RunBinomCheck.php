<?php

namespace App\Http\Controllers;

use App\Binom\Commands\CheckStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RunBinomCheck extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->user()->isAdmin()) {
            Artisan::queue(CheckStatistics::class);

            return response('Queued', 200);
        }

        return response('You are not authorized for that.', 403);
    }
}
