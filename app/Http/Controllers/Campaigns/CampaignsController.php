<?php

namespace App\Http\Controllers\Campaigns;

use App\Facebook\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CampaignsController extends Controller
{
    /**
     * Index all campaigns
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Campaign::visible()
            ->searchIn(['id', 'name', 'account_id'], $request->get('search'))
            ->with(['account', 'account.profile'])
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->orderByRaw('spend DESC NULLS LAST')
            ->orderBy('updated_at', 'desc')
            ->orderBy('account_id', 'desc')
            ->paginate();
    }
}
