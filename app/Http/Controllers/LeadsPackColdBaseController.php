<?php

namespace App\Http\Controllers;

use App\AdsBoard;
use App\Http\Requests\Leads\PackColdBase;
use Illuminate\Support\Facades\Artisan;

class LeadsPackColdBaseController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param PackColdBase $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(PackColdBase $request)
    {
        Artisan::queue('generate:cold', [
            'offer'    => $request->input('offer'),
            '--since'  => $request->input('since'),
            '--until'  => $request->input('until'),
            '--amount' => $request->input('amount'),
        ])->onQueue(AdsBoard::QUEUE_DEFAULT);

        return response()->noContent();
    }
}
