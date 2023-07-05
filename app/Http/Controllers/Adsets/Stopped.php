<?php

namespace App\Http\Controllers\Adsets;

use App\Http\Controllers\Controller;
use App\StoppedAdset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Stopped extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return StoppedAdset::query()
            ->when($request->since && $request->until, function (Builder $query) use ($request) {
                $query->whereBetween(DB::raw('created_at::date'), [$request->since, $request->until]);
            })
            ->with(['account.profile.user', 'ad'])
            ->paginate();
    }
}
