<?php

namespace App\Http\Controllers;

use App\Facebook\Ad;
use App\Lead;
use Illuminate\Http\Request;

class UtmSource extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     *
     * @return Ad[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     *
     */
    public function __invoke(Request $request)
    {
        return cache()->remember('user:'.$request->user()->id.':utm-sources', now()->addHour(), fn () => Lead::visible()
            ->select('utm_source')
            ->searchIn(['utm_source'], $request->get('search'))
            ->distinct('utm_source')
            ->orderBy('utm_source')
            ->when(
                $request->boolean('paginate'),
                fn ($query) => $query->paginate(),
                fn ($query) => $query->get()
            )
            ->pluck('utm_source'));
    }
}
