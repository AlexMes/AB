<?php

namespace App\Http\Controllers;

use App\Facebook\Campaign;
use Illuminate\Http\Request;

class UtmCampaigns extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response|\Illuminate\Support\Collection
     *
     */
    public function __invoke(Request $request)
    {
        return Campaign::query()->whereNotNull('offer_id')
            ->select('utm_key')
            ->searchIn(['utm_key'], $request->get('search'))
            ->distinct()
            ->orderBy('utm_key')
            ->when(
                $request->boolean('paginate'),
                fn ($query) => $query->paginate(),
                fn ($query) => $query->get()
            )
            ->pluck('utm_key');
    }
}
