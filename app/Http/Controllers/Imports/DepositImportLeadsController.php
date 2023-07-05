<?php

namespace App\Http\Controllers\Imports;

use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;

class DepositImportLeadsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Lead|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Request $request)
    {
        return Lead::query()
            ->when(
                $request->searchField === 'id',
                fn ($q) => $q->whereIn('id', $request->search),
                function ($q) use ($request) {
                    $pattern = implode('|', $request->search);

                    $q->where($request->searchField, 'SIMILAR TO', "%({$pattern})");
                }
            )
            ->with('offer', 'deposits')
            ->limit(1000)
            ->get();
    }
}
