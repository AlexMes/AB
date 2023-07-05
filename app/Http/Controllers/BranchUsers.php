<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;

class BranchUsers extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function __invoke(Branch $branch, Request $request)
    {
        $this->authorize('view', $branch);

        return $branch->users()
            ->orderBy('name')
            ->paginate();
    }
}
