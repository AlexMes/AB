<?php

namespace App\Http\Controllers\Ads;

use App\Facebook\AdDisapproval;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdsDisapprovalsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return AdDisapproval[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Request $request)
    {
        return AdDisapproval::query()
            ->when($request->since && $request->until, function (Builder $query) use ($request) {
                $query->whereBetween(DB::raw('created_at::date'), [$request->since, $request->until]);
            })
            ->when($request->users, function (Builder $query) use ($request) {
                $query->whereHas('ad.account.profile', fn ($q) => $q->whereIn('user_id', $request->users));
            })
            ->when($request->search, function (Builder $query) use ($request) {
                $query->whereHas('ad', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
            })
            ->notEmptyWhereIn('reason', $request->reasons)
            ->with(['ad'])
            ->take(100)
            ->get();
    }
}
