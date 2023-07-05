<?php

namespace App\Http\Controllers;

use App\Facebook\Ad;
use App\Lead;
use Illuminate\Http\Request;

class UtmContent extends Controller
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
        return cache()->remember('user:'.$request->user()->id.':utm-contents', now()->addHour(), fn () => Lead::visible()
            ->select('utm_content')
            ->searchIn(['utm_content'], $request->get('search'))
            ->distinct('utm_content')
            ->orderBy('utm_content')
            ->when(
                $request->boolean('paginate'),
                fn ($query) => $query->paginate(),
                fn ($query) => $query->get()
            )
            ->pluck('utm_content'));
    }
}
