<?php

namespace App\Http\Controllers;

use App\Http\Requests\Leads\DeleteDuplicates;
use App\Lead;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadsDeleteDuplicatesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param DeleteDuplicates $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(DeleteDuplicates $request)
    {
        $duplicates = Lead::visible()
            ->duplicates([$request->input('since'), $request->input('until')], $request->input('offers'))
            ->with(['assignments:id,lead_id'])
            ->whereBetween(DB::raw('leads.created_at::date'), [$request->input('since'), $request->input('until')])
            ->whereIn('offer_id', $request->input('offers'))
            ->orderBy('created_at')
            ->get(['id', 'phone'])
            ->groupBy('phone');

        /** @var Collection $leads */
        foreach ($duplicates as $phone => $leads) {
            $withoutAssignments = $leads->reject(fn (Lead $lead) => $lead->assignments->count() > 0);
            if ($leads->count() !== $withoutAssignments->count()) {
                // delete all leftovers duplicates if they have assignments(except assigned of course)
                $withoutAssignments->each->delete();
            } else {
                // delete leftovers duplicates if they have 0 assignments, except 1 newest
                $leads->take($leads->count() - 1)->each->delete();
            }
        }

        return response()->noContent();
    }
}
