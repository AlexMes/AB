<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResellBatches\LeadsList;
use App\Lead;
use App\LeadOrderAssignment;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ResellBatchLeadsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ResellBatches\LeadsList $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(LeadsList $request)
    {
        return response()->json(
            Lead::visible()->allowedOffers()->whereIn(
                'id',
                LeadOrderAssignment::visible()
                    ->select(['lead_id'])
                    ->when($request->input('registered_at'), function ($query, $input) {
                        return $query->whereBetween(DB::raw('DATE(registered_at)'), $input);
                    })
                    ->when($request->input('created_at'), function ($query, $input) {
                        return $query->whereBetween(DB::raw('DATE(created_at)'), $input);
                    })
                    ->when($request->input('office'), function ($query, $input) {
                        return $query->whereHas('route.order', fn ($q) => $q->whereIn('office_id', $input));
                    })
                    ->when($request->input('except_office'), function ($query, $input) {
                        return $query->whereDoesntHave('route.order', fn ($q) => $q->whereIn('office_id', $input));
                    })
                    ->when(
                        $request->input('offer'),
                        fn ($query, $input) => $query->whereHas('route', fn ($q) => $q->whereIn('offer_id', $input))
                    )
                    ->when($request->input('status'), function ($query, $input) {
                        return $query->where(function ($q) use ($input) {
                            return $q->whereIn('status', $input)
                                ->when(
                                    in_array('Новый', $input),
                                    fn ($q1) => $q1->orWhereNull('status')
                                );
                        });
                    })
                    ->when($request->input('except_status'), function ($query, $input) {
                        return $query->where(function ($q) use ($input) {
                            return $q->whereNotIn('status', $input)
                                ->unless(
                                    in_array('Новый', $input),
                                    fn ($q1) => $q1->orWhereNull('status')
                                );
                        });
                    })
                    ->notEmptyWhereIn('age', $request->input('age'))
                    ->pluck('lead_id')
                    ->unique()
                    ->toArray()
            )
                ->when($request->input('country'), function ($query, $input) {
                    return $query->whereHas('ipAddress', fn ($q) => $q->whereIn('ip_adresses.country', $input));
                })
                ->whereNotIn(
                    'id',
                    DB::table('lead_resell_batch')
                        ->distinct()
                        ->pluck('lead_id')
                )
                ->whereNotExists(function (Builder $builder) {
                    return $builder->select(DB::raw(1))
                        ->from('deposits')
                        ->whereColumn('leads.phone', 'deposits.phone');
                })
                ->with('assignments:id,status,lead_id', 'ipAddress:ip,country_name')
                ->when($request->input('limit'), fn ($query) => $query->limit($request->input('limit')))
                ->get()
        );
    }
}
