<?php

namespace App\Http\Controllers\LeadsOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassSetLeadBenefit as Request;
use App\LeadOrderAssignment;
use App\LeadPaymentCondition;

class MassSetLeadBenefit extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $leadQuery = LeadOrderAssignment::visible()
            ->when($request->input('since'), fn ($query, $input) => $query->whereDate('created_at', '>=', $input))
            ->when($request->input('until'), fn ($query, $input) => $query->whereDate('created_at', '<=', $input))
            ->when($request->input('office'), function ($query, $input) {
                return $query->whereHas('route.order', fn ($q) => $q->where('office_id', $input));
            })
            ->when($request->input('offer'), function ($query, $input) {
                return $query->whereHas('route', fn ($q) => $q->where('offer_id', $input));
            });

        $leadQuery->update([
            'benefit' => $request->input('to_model') === LeadPaymentCondition::CPL
                ? $request->input('benefit')
                : 0,
        ]);

        $leadQuery->get()->each(function (LeadOrderAssignment $assignment) use ($request) {
            optional($assignment->getDeposit())->updateBenefit(
                $request->input('to_model') === LeadPaymentCondition::CPA
                    ? $request->input('benefit')
                    : 0
            );
        });

        return response()->noContent();
    }
}
