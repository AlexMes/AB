<?php

namespace App\Deluge\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Lead;
use App\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class LeadsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('deluge::leads.index', [
            'offers' => Offer::allowed()
                ->current()
                ->orderByDesc('id')
                ->get(['id', 'name']),
            'leads' => Lead::whereIn('user_id', auth()->user()->sharedUsers()->pluck('users.id')->push(auth()->id())->toArray())
                ->with(['offer', 'ipAddress'])
                ->searchIn(['phone', 'firstname', 'lastname'], $request->get('search'))
                ->notEmptyWhereIn('offer_id', Arr::wrap($request->input('offer')))
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
