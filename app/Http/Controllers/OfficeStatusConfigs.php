<?php

namespace App\Http\Controllers;

use App\Office;
use App\StatusConfig;
use Illuminate\Http\Request;

class OfficeStatusConfigs extends Controller
{
    /**
     * Index office orders
     *
     * @param Office                   $office
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Office $office, Request $request)
    {
        $this->authorize('viewAny', StatusConfig::class);

        return $office->statusConfigs()
            ->orderBy('id', 'desc')
            ->when(
                $request->has('all'),
                fn ($q) => $q->get(),
                fn ($q) => $q->paginate()
            );
    }
}
