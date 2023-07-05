<?php

namespace App\Http\Controllers;

use App\Http\Requests\Leads\Delete;
use App\Http\Requests\Leads\Update;
use App\Http\Resources\LeadResource;
use App\Lead;
use App\LeadOrderAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Lead::class);
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $period = $request->get('date') ? json_decode($request->input('date'), true) : [];

        return LeadResource::collection(Lead::withTrashed()
            ->allowedOffers()
            ->visible()
            ->when($request->has('lasttrashed'), function ($query) {
                return $query->where(function ($builder) {
                    return $builder->where('deleted_at', '>', now()->subDay()->toDateTimeString())
                        ->orWhereNull('deleted_at');
                });
            })
            ->when($request->get('search'), function ($query, $input) {
                return $query->where(function ($q) use ($input) {
                    return $q->searchIn(['id', 'firstname', 'lastname', 'phone','email', 'clickid','utm_source','utm_content','domain','uuid'], $input)
                        ->orWhereIn('id', LeadOrderAssignment::searchIn(['external_id'], $input)->get('lead_id')->pluck('lead_id'));
                });
            })
            ->when($request->input('search'), fn ($q) => $q->with('lastAssignment.route.order.office'))
            ->when(
                $request->boolean('received'),
                fn ($query) => $query->received($period, $request->get('offer_id'))
            )
            ->when(
                $request->boolean('accepted'),
                function ($query) use ($period) {
                    $date = date_between($period);

                    return $query
                        ->when($date, fn ($q) => $q->whereBetween(DB::raw('created_at::date'), $date))
                        ->unless($date, fn ($q) => $q->whereDate('created_at', now()));
                }
            )
            ->when(
                $request->boolean('leftovers'),
                fn ($query) => $query->leftovers($period, $request->get('offer_id'))
            )
            ->when($request->boolean('resell_received'), fn ($query) => $query->resellReceived())
            ->when($request->filled('offer_id'), function (Builder $builder) use ($request) {
                return $builder->where('offer_id', $request->get('offer_id'));
            })
            ->when($request->has('user_id'), function (Builder $builder) use ($request) {
                if ($request->input('user_id') === null) {
                    return $builder->whereNull('user_id');
                }

                return $builder->where('user_id', $request->get('user_id'));
            })
            ->when($request->has('domain_id'), fn ($query) => $query->where('landing_id', $request->get('domain_id')))
            ->when($request->has('geo_country'), function (Builder $builder) use ($request) {
                return $builder->whereHas(
                    'ipAddress',
                    fn ($query) => $query->where('country_name', $request->input('geo_country'))
                );
            })
            ->with(['user:id,name','offer:id,name', 'account', 'ipAddress:ip,country_name'])
            ->orderByDesc('created_at')
            ->paginate(30));
    }

    /**
     * Load single lead details
     *
     * @param \App\Lead $lead
     *
     * @return \App\Lead
     */
    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        return $lead->load(['adset','campaign','account','offer','user', 'affiliate']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update $request
     * @param Lead   $lead
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, Lead $lead)
    {
        $lead->update($request->validated());

        return response()->json($lead->loadMissing(['user:id,name','offer:id,name', 'account', 'ipAddress:ip,country_name']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Delete $request
     * @param Lead   $lead
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Delete $request, Lead $lead)
    {
        $lead->delete();

        return response()->json($lead->loadMissing(['user','offer','account']));
    }
}
