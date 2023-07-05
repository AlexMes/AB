<?php

namespace App\Http\Controllers\Ads;

use App\Facebook\Ad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Ad[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Request $request)
    {
        return Ad::query()
            ->visible()
            ->searchIn(['name'], $request->search)
            ->take(100)
            ->get();
    }
}
