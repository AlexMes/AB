<?php

namespace App\Http\Controllers\Adsets;

use App\Facebook\AdSet;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdsetsController extends Controller
{
    /**
     * List all adsets
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return AdSet::visible()
            ->searchIn(['id', 'name', 'account_id','campaign_id'], $request->get('search'))
            ->with('account', 'account.profile')
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->orderByRaw('spend DESC NULLS LAST')
            ->orderBy('updated_at', 'desc')
            ->paginate();
    }
}
