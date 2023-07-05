<?php

namespace App\Http\Controllers;

use App\Office;
use Illuminate\Http\Request;

class OfficeUsers extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Office $office, Request $request)
    {
        return $office->users()
            ->orderBy('report_sort')
            ->when($request->has('paginate'), fn ($q) => $q->paginate(), fn ($q) => $q->get());
    }
}
