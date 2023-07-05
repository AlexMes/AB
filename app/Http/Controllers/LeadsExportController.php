<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Exports\LeadsExport;
use App\Http\Requests\Leads\LeadsExport as Request;
use App\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class LeadsExportController extends Controller
{

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('export', Lead::class);

        $leads = Lead::visible()
            ->when($request->leftovers, function (Builder $builder) {
                return $builder->leftovers();
            })
            ->when($request->has('since'), fn ($q) => $q->whereBetween('created_at', [
                Carbon::parse($request->since)->startOfDay()->toDateTimeString(),
                Carbon::parse($request->until)->endOfDay()->toDateTimeString()
            ]))
            ->when($request->offer_id, fn ($q) => $q->where('offer_id', $request->offer_id))
            ->when($request->office_id, function ($query) use ($request) {
                $query->whereHas('user', function (Builder $query) use ($request) {
                    $query->where('office_id', $request->office_id);
                });
            })
            ->when($request->branch_id, function ($query) use ($request) {
                $query->whereHas('user', function (Builder $query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                });
            })
            ->get();

        return Excel::download(new LeadsExport($leads), 'leads.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
